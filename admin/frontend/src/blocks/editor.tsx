import { LexicalComposer } from "@lexical/react/LexicalComposer";
import { ContentEditable } from "@lexical/react/LexicalContentEditable";
import { LexicalErrorBoundary } from "@lexical/react/LexicalErrorBoundary";
import { RichTextPlugin } from "@lexical/react/LexicalRichTextPlugin";
import { createRoot } from "react-dom/client";
import css from "./editor.module.css";
import { useLexicalComposerContext } from "@lexical/react/LexicalComposerContext";
import { FaBold, FaItalic } from "react-icons/fa";
import {
  $createParagraphNode,
  $getRoot,
  $getSelection,
  $isBlockElementNode,
  $isParagraphNode,
  $isRangeSelection,
  $isRootNode,
  $isTextNode,
  COMMAND_PRIORITY_CRITICAL,
  FORMAT_TEXT_COMMAND,
  LexicalEditor,
  RangeSelection,
  SELECTION_CHANGE_COMMAND,
} from "lexical";
import { $setBlocksType } from "@lexical/selection";
import {
  $createHeadingNode,
  $isHeadingNode,
  HeadingNode,
  HeadingTagType,
} from "@lexical/rich-text";
import { OnChangePlugin } from "@lexical/react/LexicalOnChangePlugin";
import { $generateHtmlFromNodes, $generateNodesFromDOM } from "@lexical/html";
import { StrictMode, useCallback, useEffect, useState } from "react";

type BlockType = "p" | "h1" | "h2" | "h3" | "h4" | "h5" | "h6";
function $getBlockType(selection: RangeSelection): BlockType {
  let anchorNode = selection.anchor.getNode();
  let parent;

  while (
    !$isBlockElementNode(anchorNode) &&
    (parent = anchorNode.getParent()) !== null
  ) {
    anchorNode = parent;
  }

  if ($isRootNode(anchorNode) || $isParagraphNode(anchorNode) || $isTextNode(anchorNode)) {
    return "p";
  } else if ($isHeadingNode(anchorNode)) {
    return anchorNode.getTag();
  }

  throw new Error(
    "Unknown block type for selection's anchor node: " + anchorNode.getType(),
  );
}

function ToolbarPlugin() {
  const [editor] = useLexicalComposerContext();
  const [selectedBlockType, setSelectedBlockType] = useState("p");

  const $updateToolbar = useCallback(() => {
    const selection = $getSelection();

    if (!$isRangeSelection(selection)) {
      return;
    }

    const blockType = $getBlockType(selection);
    setSelectedBlockType(blockType);
  }, [editor]);

  useEffect(() => {
    return editor.registerCommand(
      SELECTION_CHANGE_COMMAND,
      () => {
        $updateToolbar();

        return false;
      },
      COMMAND_PRIORITY_CRITICAL,
    );
  });

  return (
    <div className={css.toolbar}>
      <select
        onChange={(event) => {
          editor.update(() => {
            const selection = $getSelection();

            if ($isRangeSelection(selection)) {
              $setBlocksType(selection, () =>
                event.target.value === "p"
                  ? $createParagraphNode()
                  : $createHeadingNode(event.target.value as HeadingTagType),
              );
            }
          });
        }}
        value={selectedBlockType}
      >
        <option value="p">Paragraph</option>
        <option value="h1">Heading 1</option>
        <option value="h2">Heading 2</option>
        <option value="h3">Heading 3</option>
      </select>
      <button
        onClick={(e) => {
            e.preventDefault();
            editor.dispatchCommand(FORMAT_TEXT_COMMAND, "bold");
        }}
      >
        <FaBold />
      </button>
      <button
        onClick={e => {e.preventDefault(); editor.dispatchCommand(FORMAT_TEXT_COMMAND, "italic");}}
      >
        <FaItalic />
      </button>
    </div>
  );
}

function Editor({
  name,
  initialContent,
}: {
  name: string;
  initialContent: string;
}) {
  const initialConfig = {
    namespace: "BlocksEditor",
    theme: {},
    onError: console.error,
    nodes: [HeadingNode],
    editorState: (editor: LexicalEditor) => {
        if (initialContent.trim() === '') {
            return;
        }
      const domParser = new DOMParser();
      const domDocument = domParser.parseFromString(
        initialContent,
        "text/html",
      );

      const initialNodes = $generateNodesFromDOM(editor, domDocument);
      const root = $getRoot();

      root.append(...initialNodes);
    },
  };
  const [content, setContent] = useState("");

  return (
    <>
      <input type="hidden" value={content} name={name} />

      <LexicalComposer initialConfig={initialConfig}>
        <ToolbarPlugin />
        <RichTextPlugin
          contentEditable={<ContentEditable className={css.editor} />}
          ErrorBoundary={LexicalErrorBoundary}
        />
        <OnChangePlugin
          onChange={(_editorState, editor, _tags) => {
            editor.read(() => {
              const newContent = $generateHtmlFromNodes(editor, null);
              setContent(newContent);
            });
          }}
        />
      </LexicalComposer>
    </>
  );
}

document.addEventListener("DOMContentLoaded", () => {
  const rootNode = document.getElementById("block-editor")!;
  const newRootNode = document.createElement("div");
  rootNode.replaceWith(newRootNode);

  const root = createRoot(newRootNode);

  root.render(
    <StrictMode>
      <Editor name="content" initialContent={rootNode.innerText} />
    </StrictMode>,
  );
});

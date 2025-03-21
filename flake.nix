{
  description = "Root flake for my machines";

  inputs = {
    nixpkgs.url = "nixpkgs/nixos-unstable-small";
  };

  outputs = {nixpkgs, ...}: let
    pkgs = import nixpkgs {system = "x86_64-linux";};
  in {
    formatter.x86_64-linux = pkgs.alejandra;
    devShells.x86_64-linux.default = pkgs.mkShell {
      packages = with pkgs; [
        nil
        nodePackages_latest.typescript-language-server
        nodejs_22
        php84Packages.composer
        phpactor
        (php84.buildEnv {
          extensions = {enabled, all}: enabled ++ [all.xdebug];
          extraConfig = ''
            error_reporting=E_ALL | ~E_DEPRECATED
            memory_limit=1G
          '';
        })
        typescript
        postgresql_17
        shellcheck
      ];
    };
  };
}

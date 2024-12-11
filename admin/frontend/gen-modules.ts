import {resolveConfig, preprocessCSS} from 'vite';
import fs from 'fs';
import viteConfig from './vite.config';

let files = {
    'login': 'login.module.css'
};

let config = await resolveConfig(viteConfig, 'build');

for(const [name, file] of Object.entries(files)) {
    let cssCode = fs.readFileSync(config.root + '/src/' + file).toString();
    let resolved = await preprocessCSS(
        cssCode, 
        file, 
        config
    );
    fs.mkdirSync('./dist-server/css-modules/', {recursive: true});
    fs.writeFileSync('./dist-server/css-modules/' + name + '.json', JSON.stringify(resolved.modules));

}

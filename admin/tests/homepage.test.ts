import {beforeAll, describe, expect, it} from '@jest/globals';

describe('homepage', () => { 
    beforeAll(async () => {
        await page.goto('http://localhost:7979/');
    });
    
    it('Should say hello', async () => {
        const content = await page.content();
        expect(content).toContain('Hello');
    });
});

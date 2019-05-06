const puppeteer = require('puppeteer');

(async () => {
    const browser = await puppeteer.launch({args: ['--no-sandbox']});
    const page = await browser.newPage();
    var redirect_uri = encodeURIComponent(process.env.REDIRECT_URI);
    var url = 'https://api.iijmio.jp/mobile/d/v1/authorization/?response_type=token&client_id=' + process.env.CLIENT_ID + '&state=test&redirect_uri=' + redirect_uri;

    await page.goto(url);

    await page.type('input[name="username"]', process.env.USERNAME);
    await page.type('input[name="password"]', process.env.PASSWORD);

    await page.click('#submit');

    await page.waitForNavigation();

    await page.click('#confirm');
    await page.waitForNavigation();

    const redirect_url = await page.evaluate(() => location.href);

    console.log(redirect_url);

    await browser.close();
})();

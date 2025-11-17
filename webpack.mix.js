let mix = require("laravel-mix");
const tailwindcss = require("tailwindcss");

mix.browserSync({
  proxy: "wp.test.local",
});

mix
  .setPublicPath("dist")
  .js("assets/js/app.js", "js")
  .copy("assets/images/*", "dist/images")
  .sass("./assets/scss/app.scss", "/css")
  .options({
    processCssUrls: false,
    postCss: [tailwindcss("./tailwind.config.js")],
  });

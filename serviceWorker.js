const pxzone = "pxzone-v1"
const assets = [
  "/",
  "assets/js/jquery-3.6.3.min.js",
  "assets/js/auth/app.js",
  "assets/js/auth/_csrf.js",
  "assets/js/_webapp.js",
  "assets/js/_web_package.min.js",
  "assets/images/logo/hh-logo.webp",
  "assets/images/logo/mm-logo.webp",
  "assets/images/logo/hh-logo-light.webp",
  "assets/images/thumbnail.webp",
  "assets/images/other/loader.gif",
]
self.addEventListener("install", installEvent => {
  installEvent.waitUntil(
    caches.open(pxzone).then(cache => {
      cache.addAll(assets)
    })
  )
})


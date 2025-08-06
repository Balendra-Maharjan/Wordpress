/**
 * Waits for all images within a selector to load
 * @param {string} selector - CSS selector for the container element
 * @returns {Promise} - Resolves when all images are loaded
 */
export function waitForImages(selector) {
  return new Promise((resolve) => {
    const elements = document.querySelectorAll(selector);
    let loadedImages = 0;
    let totalImages = 0;

    elements.forEach(element => {
      const images = element.getElementsByTagName('img');
      totalImages += images.length;

      Array.from(images).forEach(img => {
        if (img.complete) {
          loadedImages++;
          if (loadedImages === totalImages) resolve();
        } else {
          img.addEventListener('load', () => {
            loadedImages++;
            if (loadedImages === totalImages) resolve();
          });
          img.addEventListener('error', () => {
            loadedImages++;
            if (loadedImages === totalImages) resolve();
          });
        }
      });
    });

    // If no images, resolve immediately
    if (totalImages === 0) resolve();
  });
}

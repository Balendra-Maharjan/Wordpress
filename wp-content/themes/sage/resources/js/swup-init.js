import Swup from 'swup';

const swup = new Swup();

swup.hooks.on('page:view', () => {
  console.log('Swup: Page transition complete, triggering theme:load');
  document.dispatchEvent(new CustomEvent('theme:load'));
});

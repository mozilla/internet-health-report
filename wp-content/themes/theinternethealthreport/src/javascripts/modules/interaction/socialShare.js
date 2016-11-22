import $ from 'jquery';
window.$ = $;

const windowPopup = (url, width, height) => {
  // Calculate the position of the popup so it’s centered on the screen.
  const left = (screen.width / 2) - (width / 2);
  const top = (screen.height / 2) - (height / 2);

  window.open(
    url,
    ``,
    `menubar=no,toolbar=no,resizable=yes,scrollbars=yes,width=${width},height=${height},top=${top},left=${left}`
  );
};

const socialShare = () => {
  const $shareLinks = $(`.js-social-share`);

  $shareLinks.on(`click`, (e) => {
    const shareUrl = $(e.currentTarget).attr(`href`);

    e.preventDefault();

    windowPopup(shareUrl, 500, 300);
  });
};

export { socialShare };

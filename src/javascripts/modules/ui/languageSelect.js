import $ from 'jquery';
window.$ = $;

const locales = {
  english: `en`,
  français: `fr`
};

const renderLanguageSelect = () => {
  const $el = $(`.js-language`);
  const currentLanguage = $el.data(`language`);
  const $select = $(`<select />`).addClass(`language js-language-select`);
  let locale;

  for (locale in locales) {
    if ({}.hasOwnProperty.call(locales, locale)) {
      const localeString = locale.charAt(0).toUpperCase() + locale.substring(1);
      const selected = locale === currentLanguage ? `selected` : ``;
      const $option = $(`<option value="${locales[locale]}" ${selected}>${localeString}</option>`);

      $select.append($option);
    }
  }

  $select.on(`change`, (e) => {
    const selectValue = e.currentTarget.value;
    const localeDir = selectValue === `en` ? `` : selectValue;
    const url = window.location.href;
    const protocol = window.location.protocol;
    const hostname = window.location.hostname;
    const port = window.location.port === `3000` ? `:3000` : ``;
    const origin = window.location.origin || `${protocol}//${hostname}${port}`;
    const targetDirectory = url.replace(origin, ``).replace(`/${locales[currentLanguage]}/`, ``);
    const targetURL = `${origin}/${localeDir}${targetDirectory}`;

    window.location.replace(targetURL);
  });

  $el.append($select);
};

export { renderLanguageSelect };

/* global Waypoint */
import $ from 'jquery';
import '../../plugins/noframework.waypoints';
window.$ = $;

class Custom {
  constructor(el, labelEnglish, labelRussian, labelGerman, labelJapanese, labelSpanish, labelFrench, labelPortugese, labelChinese, labelArabic) {
    this.el = el;
    this.labelEnglish = labelEnglish;
    this.labelRussian = labelRussian;
    this.labelGerman = labelGerman;
    this.labelJapanese = labelJapanese;
    this.labelSpanish = labelSpanish;
    this.labelFrench = labelFrench;
    this.labelPortugese = labelPortugese;
    this.labelChinese = labelChinese;
    this.labelArabic = labelArabic;
  }

  render() {
    $(this.el).find(`#custom-english`).text(this.labelEnglish);
    $(this.el).find(`#custom-russian`).text(this.labelRussian);
    $(this.el).find(`#custom-german`).text(this.labelGerman);
    $(this.el).find(`#custom-japanese`).text(this.labelJapanese);
    $(this.el).find(`#custom-spanish`).text(this.labelSpanish);
    $(this.el).find(`#custom-french`).text(this.labelFrench);
    $(this.el).find(`#custom-portugese`).text(this.labelPortugese);
    $(this.el).find(`#custom-chinese`).text(this.labelChinese);
    $(this.el).find(`#custom-arabic`).text(this.labelArabic);

    const waypoint = new Waypoint({
      element: document.getElementById(this.el.substr(1)),
      handler: () => {
        $(this.el).addClass(`is-active`);
        waypoint.destroy();
      },
      offset: `50%`,
    });
  }
}

const loadCustomCharts = () => {
  const $custom = $(`.js-custom`);

  $custom.each((index) => {
    const $this = $custom.eq(index);
    const id = $this.attr(`id`);
    const labelEnglish = $this.data(`label-english`);
    const labelRussian = $this.data(`label-russian`);
    const labelGerman = $this.data(`label-german`);
    const labelJapanese = $this.data(`label-japanese`);
    const labelSpanish = $this.data(`label-spanish`);
    const labelFrench = $this.data(`label-french`);
    const labelPortugese = $this.data(`label-portugese`);
    const labelChinese = $this.data(`label-chinese`);
    const labelArabic = $this.data(`label-arabic`);

    new Custom(`#${id}`, labelEnglish, labelRussian, labelGerman, labelJapanese, labelSpanish, labelFrench, labelPortugese, labelChinese, labelArabic).render();
  });
};

export { loadCustomCharts };

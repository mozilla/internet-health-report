// Visualizations

import { loadChoropleths } from './modules/visualizations/Choropleth';
loadChoropleths();

import { loadDonuts } from './modules/visualizations/Donut';
loadDonuts();

import { loadStackedAreaCharts } from './modules/visualizations/StackedArea';
loadStackedAreaCharts();

import { loadAreaCharts } from './modules/visualizations/Area';
loadAreaCharts();

import { loadBarCharts } from './modules/visualizations/Bar';
loadBarCharts();

import { loadBarHorizontalCharts } from './modules/visualizations/BarHorizontal';
loadBarHorizontalCharts();

import { loadBarStackedCharts } from './modules/visualizations/BarStacked';
loadBarStackedCharts();

import { loadLineCharts } from './modules/visualizations/Line';
loadLineCharts();

import { loadMultiLineCharts } from './modules/visualizations/MultiLine';
loadMultiLineCharts();

import { loadBubbleCharts } from './modules/visualizations/Bubble';
loadBubbleCharts();

// UI

import { renderLanguageSelect } from './modules/ui/languageSelect';
renderLanguageSelect();

// Interaction

import { smoothScrollInit } from './modules/interaction/smoothScroll';
smoothScrollInit();

import { embedInit } from './modules/interaction/embed';
embedInit();

import { scrollHandler } from './modules/interaction/scrollHandler';
scrollHandler();

import { resizeHandler } from './modules/interaction/resizeHandler';
resizeHandler();

!function(e){"function"==typeof define&&define.amd?define(e):e()}((function(){"use strict";window.addEventListener("load",(function(){document.querySelectorAll(".accordion").forEach((t=>{new e(t)}))}));class e{constructor(e,t=!1){this.focus=t,this.container=e,this.toggle=e.querySelector(".toggle"),this.content=e.querySelector(".toggle-section"),this.button=e.querySelector(".toggle-label"),this.init()}findFirstFocusableElement(e){const t=e.querySelectorAll('a[href], button:not([disabled]), textarea:not([disabled]), input:not([type=submit]):not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'),i=Array.from(t).filter((e=>e.offsetWidth>0&&e.offsetHeight>0));return i.length>0?i[0]:null}init(){this.toggle.addEventListener("change",(()=>{if(this.toggle.checked){if(this.toggle.setAttribute("aria-expanded","true"),this.focus){let e=this.findFirstFocusableElement(this.content);e&&e.focus()}}else this.toggle.setAttribute("aria-expanded","false"),this.button.focus()}))}}!function(){const e=document.getElementById("site-header"),t=document.body,i=getComputedStyle(e),n=parseInt(i.getPropertyValue("--header-max-height"),10);let o=!1;window.addEventListener("scroll",(()=>{const i=Math.floor(window.scrollY);e.style.willChange="height",i>=n&&!o?(e.style.height="var(--header-min-height)",t.classList.add("header-collapsed"),o=!0):i<n&&o&&(e.style.height="var(--header-max-height)",t.classList.remove("header-collapsed"),o=!1)}))}()}));
//# sourceMappingURL=index.js.map

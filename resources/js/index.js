import hljs from "highlight.js";
import sql from "highlight.js/lib/languages/sql";

hljs.registerLanguage("sql", sql);

hljs.initHighlighting();

document.querySelectorAll(".language-sql").forEach((e) => {
    hljs.highlightElement(e);
});

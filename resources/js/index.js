import hljs from "highlight.js/lib/core";
import sql from "highlight.js/lib/languages/sql";

hljs.registerLanguage("sql", sql);

hljs.highlightAll();

document.querySelectorAll(".language-sql").forEach((e) => {
    hljs.highlightElement(e);
});

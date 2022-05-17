import InfiniteScroll from 'infinite-scroll';

const inFiniteScrollCallable = () => {

    let paginationContainer = document.querySelector('nav.dr__pagination');

    if (!paginationContainer) return;

    let paginationType = paginationContainer.dataset.pagination;

    if (paginationType.indexOf("simple") > -1) return;
    if (paginationType.indexOf("next_prev") > -1) return;

    if (!paginationContainer.querySelector(".next")) return;

    let itemWrapper = paginationContainer.parentNode.previousElementSibling;

    let inf = new InfiniteScroll(itemWrapper, {
        checkLastPage: ".next",
        path: ".next",
        append: ".dr-archive-single",
        // outlayer,
        hideNav: paginationContainer.querySelector("nav"),
        button:
            paginationType === "load_more"
                ? paginationContainer.querySelector(".dr-load-more")
                : null,

        scrollThreshold: paginationType === "infinite_scroll" ? 400 : false,

        onInit() {
            this.on("load", (response) => {
                paginationContainer
                    .querySelector(".dr__load-more-helper")
                    .classList.remove("dr-loading");

                setTimeout(() => {

                }, 100);
            });

            this.on("append", () => {
                // watchLayoutContainerForReveal(layoutEl);
                // var masonry_element = document.querySelector('.rishi-container-wrap');
            });

            this.on("request", () => {
                paginationContainer
                    .querySelector(".dr__load-more-helper")
                    .classList.add("dr-loading");
            });

            this.on("last", () => {
                paginationContainer.classList.add(
                    !paginationContainer.querySelector(".dr__last-page-text")
                        ? "dr__last-page-no-info"
                        : "dr__last-page"
                );
            });
        },
    });
}

document.addEventListener("DOMContentLoaded", function () {
    inFiniteScrollCallable();
});
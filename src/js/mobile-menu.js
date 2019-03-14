/* global $ */
(function () {

  let menuSelector;
  let menuTriggerSelector;
  let $menuTrigger;
  let menuTriggerClass;
  let $menu;
  let menuSlideoutClass;
  let menuCloseSelector;
  let $bodyOverlay;
  let bodyOverlayClass;


  function closeMenu() {
    $menu.removeClass(menuSlideoutClass);
    $menuTrigger.removeClass(menuTriggerClass);
    $bodyOverlay.removeClass(bodyOverlayClass);
  }

  function toggleMenu() {
    $menu.toggleClass(menuSlideoutClass);
    $menuTrigger.toggleClass(menuTriggerClass);
    $bodyOverlay.toggleClass(bodyOverlayClass);
  }

  function closeMobileMenuHandler() {
    $(document).click((e) => {
      const $target = $(e.target);

      // Hide the mobile menu when anything else is clicked
      if (!$target.closest(menuSelector).length && !$target.closest(menuTriggerSelector).length) {
        if ($menu.width() > 200) {
          closeMenu();
        }
      }
    });
  }

  function closeMobileMenuIconHandler() {
    $menu.find(menuCloseSelector).click(closeMenu);
  }

  function setupEventHandlers() {
    $(menuTriggerSelector).click(toggleMenu);
  }

  function init() {
    menuSelector = '.today-site-nav';
    menuTriggerSelector = '.navbar-toggler';
    $menuTrigger = $(menuTriggerSelector);
    menuTriggerClass = 'active';
    $menu = $(menuSelector);
    menuSlideoutClass = 'slideout';
    menuCloseSelector = '.close';
    $bodyOverlay = $('#nav-overlay');
    bodyOverlayClass = 'in';

    setupEventHandlers();
    closeMobileMenuHandler();
    closeMobileMenuIconHandler();
  }

  $(init);
}());

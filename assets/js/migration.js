/**
 * Migrate from old Swift Control to the new Better Admin Bar.
 */
(function ($) {
  var isRequesting = false;
  var loading = {};
  var ajax = {};
  var migrationButton;

  var elms = {};

  elms.migrationFailed = document.querySelector(
    ".swift-control-migration-status.migration-failed"
  );
  elms.errorMessage = elms.migrationFailed.querySelector(".error-message");

  elms.uninstallOldPlugin = document.querySelector(
    ".swift-control-migration-status.old-swift-control-uninstalled"
  );
  elms.uninstallOldPluginMsg =
    elms.uninstallOldPlugin.querySelector(".process-message");

  elms.installNewPlugin = document.querySelector(
    ".swift-control-migration-status.new-better-admin-bar-installed"
  );
  elms.installNewPluginMsg =
    elms.installNewPlugin.querySelector(".process-message");

  elms.activateNewPlugin = document.querySelector(
    ".swift-control-migration-status.new-better-admin-bar-activated"
  );
  elms.activateNewPluginMsg =
    elms.activateNewPlugin.querySelector(".process-message");

  /**
   * Call the main functions here.
   */
  function init() {
    $(document).on("click", ".swift-control-migration-button", ajax.migration);
  }

  loading.start = function () {
    migrationButton.classList.add("is-loading");
  };

  loading.stop = function () {
    migrationButton.classList.remove("is-loading");
  };

  /**
   * Send ajax request to save the settings.
   */
  ajax.migration = function (e) {
    e.preventDefault();

    var confirmMsg =
      "Please don't leave this page until the migration is complete. Migrate now?";

    if (!confirm(confirmMsg)) {
      return;
    }

    if (!migrationButton) migrationButton = this;

    if (isRequesting) return;
    isRequesting = true;
    loading.start();

    var data = {};

    data.action = "swift_control_migration";
    data.nonce = SwiftControlMigration.nonces.migration;

    data.old_plugin_slug = SwiftControlMigration.oldPlugin.slug;
    data.old_plugin_basename = SwiftControlMigration.oldPlugin.basename;

    data.new_plugin_slug = SwiftControlMigration.newPlugin.slug;
    data.new_plugin_basename = SwiftControlMigration.newPlugin.basename;

    elms.uninstallOldPluginMsg.innerHTML =
      "Uninstalling old Swift Control plugin...";
    elms.uninstallOldPlugin.classList.add("is-waiting");

    $.ajax({
      url: ajaxurl,
      type: "post",
      dataType: "json",
      data: data,
    })
      .done(function (r) {
        if (!r.success) {
          elms.uninstallOldPlugin.classList.remove("is-waiting");
          elms.errorMessage.innerHTML = r.data;
          elms.migrationFailed.classList.add("is-done");
          return;
        }

        elms.uninstallOldPluginMsg.innerHTML = r.data;
        elms.uninstallOldPlugin.classList.remove("is-waiting");
        elms.uninstallOldPlugin.classList.add("is-done");

        elms.installNewPluginMsg.innerHTML =
          "Installing Better Admin Bar plugin...";
        elms.installNewPlugin.classList.add("is-waiting");

        isRequesting = false;
        installBetterAdminBar();
      })
      .fail(function (jqXHR) {
        if (jqXHR.responseJSON && jqXHR.responseJSON.data) {
          elms.errorMessage.innerHTML = jqXHR.responseJSON.data;
        }

        elms.uninstallOldPlugin.classList.remove("is-waiting");
        elms.migrationFailed.classList.add("is-done");
        loading.stop();
        isRequesting = false;
      });
  };

  function installBetterAdminBar() {
    if (isRequesting) return;
    isRequesting = true;

    wp.updates.installPlugin({
      slug: SwiftControlMigration.newPlugin.slug,
      success: function () {
        elms.installNewPluginMsg.innerHTML =
          "Better Admin Bar plugin has been installed";
        elms.installNewPlugin.classList.remove("is-waiting");
        elms.installNewPlugin.classList.add("is-done");

        elms.activateNewPluginMsg.innerHTML =
          "Activating Better Admin Bar plugin...";
        elms.activateNewPlugin.classList.add("is-waiting");

        isRequesting = false;
        activateBetterAdminBar();
      },
      error: function (jqXHR) {
        if (jqXHR.responseJSON && jqXHR.responseJSON.data) {
          elms.errorMessage.innerHTML = jqXHR.responseJSON.data;
        }

        elms.installNewPlugin.classList.remove("is-waiting");
        elms.migrationFailed.classList.add("is-done");
        loading.stop();
        isRequesting = false;
      },
    });
  }

  function activateBetterAdminBar() {
    if (isRequesting) return;
    isRequesting = true;

    $.ajax({
      async: true,
      type: "GET",
      url: SwiftControlMigration.newPlugin.activationUrl,
      success: function () {
        elms.activateNewPluginMsg.innerHTML =
          "Better Admin Bar plugin has been activated";
        elms.activateNewPlugin.classList.remove("is-waiting");
        elms.activateNewPlugin.classList.add("is-done");

        loading.stop();
        isRequesting = false;

        // Redirect to Better Admin Bar settings page.
        window.location.replace(SwiftControlMigration.redirectUrl);
      },
      error: function (jqXHR) {
        if (jqXHR.responseJSON && jqXHR.responseJSON.data) {
          elms.errorMessage.innerHTML = jqXHR.responseJSON.data;
        }

        elms.activateNewPlugin.classList.remove("is-waiting");
        elms.migrationFailed.classList.add("is-done");
        loading.stop();
        isRequesting = false;
      },
    });
  }

  init();
})(jQuery);

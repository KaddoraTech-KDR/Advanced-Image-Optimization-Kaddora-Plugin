jQuery(document).ready(function ($) {
  $("#aiok-clear-logs").on("click", function () {
    if (!confirm("Are you sure you want to clear all logs?")) {
      return;
    }

    $.post(
      aiokAdmin.ajaxUrl,
      {
        action: "aiok_clear_logs",
        nonce: aiokAdmin.nonce,
      },
      function (response) {
        if (response.success) {
          alert("Logs cleared successfully");

          location.reload();
        } else {
          alert("Failed to clear logs");
        }
      },
    );
  });
});

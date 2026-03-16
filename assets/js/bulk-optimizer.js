jQuery(document).ready(function ($) {
  $("#aiok-bulk-start").on("click", function () {
    $("#aiok-status").text("Optimizing images...");
    $("#aiok-bulk-start").prop("disabled", true);

    $.post(
      aiokBulkOptimizer.ajaxUrl,
      {
        action: aiokBulkOptimizer.action,
        nonce: aiokBulkOptimizer.nonce,
      },

      function (response) {
        if (response.success) {
          let optimized = response.data.optimized;
          let total = response.data.total;
          let saved = response.data.saved;

          $("#aiok-total-images").text(total);
          $("#aiok-optimized-images").text(optimized);
          $("#aiok-space-saved").text(saved);

          let progress = (optimized / total) * 100;

          $("#aiok-progress").css("width", progress + "%");

          $("#aiok-status").text(
            "Optimization complete. " + optimized + " images optimized",
          );

          $("#aiok-bulk-start").prop("disabled", false);
        }
      },
    );
  });
});

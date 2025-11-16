jQuery(function ($) {
  if (typeof mtVideoActions === "undefined") {
    return;
  }

  var ajaxUrl = mtVideoActions.ajax_url;
  var nonce = mtVideoActions.nonce;
  var postId = mtVideoActions.post_id;
  var isLogged = !!mtVideoActions.is_logged;
  var permalink = mtVideoActions.permalink;

  function toggleClassActive($btn, active) {
    if (active) {
      $btn.addClass("text-pink-500");
    } else {
      $btn.removeClass("text-pink-500");
    }
  }

  // Save (favorite)
  $(".js-mt-save").on("click", function (e) {
    e.preventDefault();

    if (!isLogged) {
      alert("กรุณาล็อกอินก่อนบันทึกวิดีโอ");
      return;
    }

    var $btn = $(this);

    $.post(ajaxUrl, {
      action: "mt_toggle_favorite",
      nonce: nonce,
      post_id: postId,
    }).done(function (res) {
      if (!res) return;

      if (res.success) {
        var active = !!res.data.is_favorite;
        toggleClassActive($btn, active);
        $btn.find(".js-label").text(active ? "Saved" : "Save");
      } else if (res.data === "login_required") {
        alert("กรุณาล็อกอินก่อนบันทึกวิดีโอ");
      }
    });
  });

  // Playlist
  $(".js-mt-playlist").on("click", function (e) {
    e.preventDefault();

    if (!isLogged) {
      alert("กรุณาล็อกอินเพื่อใช้ Playlist");
      return;
    }

    var $btn = $(this);

    $.post(ajaxUrl, {
      action: "mt_toggle_playlist",
      nonce: nonce,
      post_id: postId,
    }).done(function (res) {
      if (!res) return;

      if (res.success) {
        var active = !!res.data.in_playlist;
        toggleClassActive($btn, active);
        $btn.find(".js-label").text(active ? "In playlist" : "Playlist");
      } else if (res.data === "login_required") {
        alert("กรุณาล็อกอินเพื่อใช้ Playlist");
      }
    });
  });

  // Share panel toggle
  $(".js-mt-share-toggle").on("click", function (e) {
    e.preventDefault();

    // ถ้า browser รองรับ Web Share API ใช้อันนี้ก่อน
    if (navigator.share) {
      navigator
        .share({
          title: document.title,
          url: permalink,
        })
        .catch(function () {});
      return;
    }

    $("#mt-share-panel").toggleClass("hidden");
  });

  // Click outside to close share panel
  $(document).on("click", function (e) {
    var $panel = $("#mt-share-panel");
    if (!$panel.length || $panel.hasClass("hidden")) {
      return;
    }
    if (
      !$(e.target).closest("#mt-share-panel").length &&
      !$(e.target).closest(".js-mt-share-toggle").length
    ) {
      $panel.addClass("hidden");
    }
  });

  // Copy link
  $(".js-mt-copy-link").on("click", function () {
    var $input = $("#mt-share-url");
    $input.trigger("select");

    var text = $input.val();
    var copied = false;

    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard
        .writeText(text)
        .then(function () {
          copied = true;
        })
        .catch(function () {});
    }

    if (!copied) {
      try {
        copied = document.execCommand("copy");
      } catch (e) {}
    }

    if (copied) {
      var $btn = $(this);
      $btn.text("Copied");
      setTimeout(function () {
        $btn.text("Copy");
      }, 1200);
    } else {
      alert("คัดลอกไม่สำเร็จ กรุณาคัดลอกจากช่อง URL เอง");
    }
  });
});

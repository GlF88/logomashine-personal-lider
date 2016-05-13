$(document).ready(function() {

	$("#form").submit(function() {
		$.ajax({
			type: "POST",
			url: "../php/handler.php",
			data: $(this).serialize()
		}).done(function() {
			$(this).find("input").val("");
			alert("Спасибо за заявку! Скоро мы с вами свяжемся.");
			$("#form").trigger("reset");
		});
		return false;
	});

  $(".tel-input").mask("8(999) 999-9999");

  $(".header_menu").on("click","a", function (event) {
		event.preventDefault();
		var id  = $(this).attr('href'),
			  top = $(id).offset().top;
		$('body,html').animate({scrollTop: top - $(".wrap-header").height()}, 1000);
	});

  $('.tarif_block_more').click(function () {
    $('.popup-wrap').css({'transform': 'scale(1)'})
  })

  $(document).on('click', '.popup-wrap', function () {
    $('.popup-wrap').css({'transform': 'scale(0)'})
  });

  $(document).on('click', '.popup', function () {
    return false;
  });

  $('.popup-wrap').css({'transform': 'scale(0)'})
});

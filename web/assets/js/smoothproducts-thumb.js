$(".sp-wrap").append('<div class="sp-large"></div>');
$(".productThumb").append('<div class="sp-thumbs sp-tb-active"></div>');
$(".sp-wrap a").appendTo(".sp-thumbs");
$(".sp-thumbs a:first").addClass("sp-current").clone().removeClass("sp-current").appendTo(".sp-large");
$(".sp-wrap").css("display", "inline-block");
var slideTiming = 300;
var maxWidth = $(".sp-large img").width();
$(".sp-thumbs").on("click", function (e) {
    e.preventDefault()
});
$(".sp-tb-active a").on("click", function (e) {
    $(".sp-current").removeClass();
    $(".sp-thumbs").removeClass("sp-tb-active");
    $(".sp-zoom").remove();
    var t = $(".sp-large").height();
    $(".sp-large").css({overflow: "hidden", height: t + "px"});
    $(".sp-large a").remove();
    $(this).addClass("sp-current").clone().hide().removeClass("sp-current").appendTo(".sp-large").fadeIn(slideTiming, function () {
        var e = $(".sp-large img").height();
        $(".sp-large").height(t).animate({height: e}, "fast", function () {
            $(".sp-large").css("height", "auto")
        });
        $(".sp-thumbs").addClass("sp-tb-active")
    });
    e.preventDefault()
});

$(".sp-zoom").on("click", function (e) {
    $(this).fadeOut(function () {
        $(this).remove()
    })
})


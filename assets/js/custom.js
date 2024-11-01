// WP Responsive Demo Switch Bar Bar Version v1.0
var theme_list_open = false;
jQuery(document).ready(function($) {
    function e() {
        var e = $("#switch-bar").height();
        $("#iframe").attr("height", $(window).height() - e + "px")
    }
    IS_IPAD = navigator.userAgent.match(/iPad/i) != null;
    $(window).resize(function() {
        e()
    }).resize();
    $("#theme_select").click(function() {
        if (theme_list_open == true) {
            $(".switch-container ul li ul").removeClass('showul');
            theme_list_open = false
        } else {
        	$(".switch-container ul li ul").addClass('showul');
            //$(".center ul li ul").show();
            theme_list_open = true
        }
        return false
    });
    $("#theme_list ul li a").click(function() {
        var e = $(this).attr("rel").split(",");
        $("li.purchase a").attr("href", e[1]);
        $("li.remove_frame a").attr("href", e[0]);
        $("#iframe").attr("src", e[0]);
        $("#theme_list a#theme_select").text($(this).text());
        $(".switch-container ul li ul").removeClass('showul');
        theme_list_open = false;
        return false
    });
    $("#header-bar").hide();
    clicked = "desktop";
    var t = {
        desktop: "100%",
        tabletlandscape: 1024,
        tabletportrait: 768,
        mobilelandscape: 480,
        mobileportrait: 320,
        placebo: 0
    };
    jQuery(".responsive a").on("click", function() {
        var e = jQuery(this);
        for (device in t) {
            console.log(device);
            console.log(t[device]);
            if (e.hasClass(device)) {
                clicked = device;
                jQuery("#iframe").width(t[device]);
                if (clicked == device) {
                    jQuery(".responsive a").removeClass("active");
                    e.addClass("active")
                }
            }
        }
        return false
    });
    if (IS_IPAD) {
        $("#iframe").css("padding-bottom", "60px")
    }
})
// $('#btn-expand-all').on('click', function (e) {
//     // location.href = "?show=all";
// });
// $(document).ready(function () {
//     // var str = window.location.hostname.length;
//     // var pas = window.location.href;
//     // var des = pas.substr(0, str);
//     // window.location.pathname = "";
//     // window.location.search = "";
//
//
//     console.log(window.location);
//
// });
$(document).ready(function () {
    localStorage.removeItem("Marked");
});

$(document).on('click', ".list-group-item", function (event) {

        var reply = false;
        if ($(event.target).attr('class') == "tex_level2") {
            $('.list-group-item').attr("style", "background-color:undefined;");
            reply = true;
        } else {
            var $level_text = $(event.target).children("div");
            $level_text.each(function () {
                if ($(this).hasClass("tex_level2")) {
                    $('.list-group-item').attr("style", "background-color:undefined;");
                    reply = true;
                }
            });
        }

        if (reply)
            localStorage.removeItem("Marked");


        if (!reply && $(event.target).hasClass('node-selected'))
            localStorage.removeItem("Marked");
        else {
            var $kids = $(event.target).children("span");
            $kids.each(function () {
                switch ($(this).attr("class")) {
                    case 'post':
                        localStorage.removeItem("Marked");
                        localStorage.setItem("Marked", "comment/" + $(this).text());
                        break;
                    case 'comment':
                        var $data = $(this).text().split(':');
                        localStorage.removeItem("Marked");
                        localStorage.setItem("Marked", "reply/" + $data[1]);
                        break;
                }
            });
        }
    }
).on("click", "span", function () {
    if ($(this).parent().children(".badge").text() != "0") {
        if ($(this).hasClass("glyphicon-plus-sign")) {
            var $parent = $(this).parent();
            var $kids = $parent.children("span");
            $kids.each(function () {
                if ($(this).hasClass('comment')) {
                    var $data = $(this).text().split(':');
                    window.location.search = "";
                    window.location.pathname = "";
                    window.location.href = "/blog/post/" + $data[0] + "/comment/" + $data[1];
                    // console.log();
                    // location.href = "?post_id=" + $data[0] + "&comment_id=" + $data[1];
                } else if ($(this).hasClass('post')) {
                    window.location.search = "";
                    window.location.pathname = "";
                    window.location.href = "/blog/post/" + $(this).text();
                    // console.log("blog/post/" + $(this).text());
                    // location.href = "?post_id=" + $(this).text();
                }
            });

        }
    }

}).on('click', "#btnForm", function () {
    window.location.search = "";
    window.location.pathname = "";
    if (localStorage.Marked)
        window.location.href = "/insert/" + localStorage.Marked + "/" + $.trim($("#Form").val());
    else  window.location.href = "/insert/post/" + $.trim($("#Form").val());
}).on('click', "#btn-expand-all", function () {
    window.location.search = "";
    window.location.pathname = "";
    window.location.href = "/blog/posts/all";
}).on('click', "#log_out", function () {
    window.location.search = "";
    window.location.pathname = "";
    window.location.href = "/out";
})
;


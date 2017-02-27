<!DOCTYPE html>
<html>
<head>
    <title>Bootstrap Tree View</title>
    <link href="/utilities/css/bootstrap.css" rel="stylesheet">

</head>
<body>
<div class="container ">
    <div class="row col-sm-12">

        <div class="<?php if (isset($_SESSION[IMG])): echo "col-sm-6";
        else: echo "col-sm-12";
            echo "style='margin-left: 20px'"; endif; ?> ">
            <h2 style="color:#428bca">Well Come</h2>


            <div class="form-group row">

                <div class="col-sm-6">
                    <a <?php if (empty($_SESSION[IMG])): echo "style=\"margin-left: 494px;\""; endif; ?>
                        href='<?= $hrf_vk_aut ?>'>
                        <?php if (isset($_SESSION[IMG])): ?>
                            <img class="imgvk" id="vk" src="<?= $_SESSION[IMG]; ?>" alt=""/>
                        <?php else: ?>
                            <img style="margin-left: 30px" id="vk" src="/utilities/images/vk.png" alt=""/>
                        <?php endif; ?>
                    </a>
                </div>

                <div class=<?php if (isset($_SESSION[IMG])): echo "col-sm-6";
                else: echo "col-sm-12"; endif; ?>>
                    <button type="button" class="btn btn-success" id="btn-expand-all">Expand All</button>
                </div>

                <div class=<?php if (isset($_SESSION[IMG])): echo "col-sm-6";
                else: echo "col-sm-12"; endif; ?>>
                    <button type="button" class="btn btn-danger" id="btn-collapse-all">Collapse All</button>
                </div>


                <?php if (isset($_SESSION[IMG])) : ?>
                    <div class="col-sm-12">
                        <a id="log_out" href="http://vk.com" target="_blank" class="btn btn-primary" role="button">
                            Log out
                        </a>
                    </div>
                <?php endif; ?>


            </div>


            <div id="treeview"></div>
        </div>

        <?php if (isset($_SESSION[IMG])) : ?>

            <form name="sentMessage" class="well col-sm-6">
                <div class="control-group">
                    <div class="controls">
                       <textarea id="Form" style="max-width: 515px" class="form-control" name="text"
                                 placeholder="Type in your message"
                                 rows="7" required></textarea>
                    </div>
                </div>
                <br><input id="btnForm" class="btn btn-primary col-sm-12" type="button"
                           value="Send post OR message to marked line ">
            </form>

        <?php endif; ?>

    </div>

</div>
</body>
</html>

<script src="/utilities/js/jquery.js"></script>
<script src="/utilities/js/bootstrap-treeview.js"></script>
<script src="/utilities/js/user.js"></script>
<script src="/utilities/js/User.js"></script>
<script type="text/javascript">
    $(function () {

        var defaultData = [

            <?php foreach ($arPosts as $post) {
            echo "
            {
                text: '" . $post->time . "<br>" . addslashes($post->text) . "<span  class=\"post\" hidden>" . $post->id . "</span>" . "',
                tags: ['" . $post->get_count_comment() . "'],
                nodes: [";

            foreach ($post->comments as $array) {
                echo "
                    {
                        text: '" . $array[COMMENT][C_TIME] . "<br><span class=\"indent\"></span>" . addslashes($array[COMMENT][C_TEXT]) . "<span class=comment hidden>" . $post->id . ":" . $array[COMMENT][C_ID] . "</span>" . "',
                        tags: ['" . $post->get_count_replies($array[COMMENT][C_ID]) . "'],
                        nodes: [";
                foreach ($array[COMMENT][REPLIES] as $reply) {
                    echo "
                           {
                             text: '" . $reply[R_TIME] . "<br><div class=\"tex_level2\">" . addslashes($reply[R_TEXT]) . "</div>" . "',
                           },";
                };

                echo "]},";
            }
            echo "  ]},";

        }?>];


        var $Tree = $('#treeview').treeview({
            data: defaultData,
            color: "#428bca",
            expandIcon: "glyphicon glyphicon-plus-sign",
            collapseIcon: "glyphicon glyphicon-minus-sign",
            nodeIcon: "glyphicon glyphicon-calendar",
            showTags: true
        });

        $('#btn-collapse-all').on('click', function (e) {
            $Tree.treeview('collapseAll', {silent: $('#chk-expand-silent').is(':checked')});
        });

    });
</script>





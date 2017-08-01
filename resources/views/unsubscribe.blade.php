<!DOCTYPE html">

<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <meta name="robots" content="noindex" />
    <title>
        You have successfully been removed from this subscriber list
    </title>
    <link rel="stylesheet" href="/css/unsubscribe.css" type="text/css">
</head>

<body>
    <div class="container">
        <div class="title">
            <div class="completed" style="display: none">
                <h2>Thank You</h2>
                <p>You have been successfully removed from this subscriber list. You will no longer hear from us.</p>
            </div>
            <noscript>
                <h2>Unsubscribe from this list</h2>
                <p>To unsubscribe from this list, please click the confirmation button below and you will be instantly removed.</p>
            </noscript>
        </div>

        <div class="cta">
            <div id="completed" class="completed" style="display: none">

                <div class="action icon-mistake">Did you unsubscribe by accident? <a href="/emails/{{$emailer_uuid}}/r/{{$address_uuid}}">Click here to re-subscribe</a>.
                </div>

            </div>
            <noscript>
                <div id="confirm">
                    <form method="put">
                        <input type="submit" value="Please unsubscribe me" />
                    </form>
                </div>
            </noscript>
        </div>

    </div>


    <script type="text/javascript" src="/build/js/vendor/jquery.min.js"></script>

    <script type="text/javascript">
        function clean(input) {
            return input.replace('http://blackholemyemail.com/', '');
        }

        $(document).ready(function() {
            var unsubUrl = 'http://blackholemyemail.com//emails/{{$emailer_uuid}}/u/{{$address_uuid}}?ajax=t';

            $.ajax({
                url: clean(unsubUrl),
                dataType: 'json',
                cache: false,
                type: 'PUT',
                success: function(json) {
                    if (json.redirect) {
                        window.location = json.redirect;
                    } else {
                        $('.completed,#pending').toggle();
                    }
                }
            });
        });
    </script>

</body>

</html>

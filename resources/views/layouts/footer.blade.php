<footer class="footer myfooter">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <script>document.write(new Date().getFullYear())</script> © Ronda
            </div>
        </div>
    </div>
    <button class="btn scrollToTopBtn"><i class="material-icons">arrow_upward</i></button>
</footer>

<script>
    $(function () {
        let config = {
            root    : '.ldld.light.full',
            autoZ   : true,
        };
        let ldld = new ldLoader(config);
        
        // any form submit will trigger the loader spinner
        $('form').submit(function (e) {
            ldld.toggle();
        });

        // any ajax call will trigger the loader spinner
        $(document).on({
            ajaxStart: function(){
                ldld.toggle();
            },
            ajaxStop: function(){ 
                ldld.off();
            }    
        });

        window.addEventListener('ldld-event', event => {
        if(event.detail.type == 'open'){
            ldld.toggle();
        }
        if(event.detail.type == 'close'){
            ldld.off();
        }
    });
    });
</script>

<script>
    
    var scrollToTopBtn = document.querySelector(".scrollToTopBtn");
    var rootElement = document.documentElement;
    
    window.onscroll = function() {scrollFunction()};

    function scrollFunction() {
        // When the user scrolls down 20px from the top of the document, show the button
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            scrollToTopBtn.classList.add("showBtn");
        } else {
            scrollToTopBtn.classList.remove("showBtn");
        }
    }

    function scrollToTop() {
        rootElement.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    }
    scrollToTopBtn.addEventListener("click", scrollToTop);

</script>

<script>
    @if (Config::get('app.env') == 'aaa')
    // Detect browser open DEVTOOLS (inspect element), then block from access the website
    !function() {
        function detectDevTool(allow) {
            if(isNaN(+allow)) allow = 100;
            var start = +new Date();
            debugger;
            var end = +new Date();
            if(isNaN(start) || isNaN(end) || end - start > allow) {
                alert('DEVTOOLS detected. all operations will be terminated.');
            document.write('DEVTOOLS detected.');
            }
        }
        if(window.attachEvent) {
            if (document.readyState === "complete" || document.readyState === "interactive") {
                detectDevTool();
            window.attachEvent('onresize', detectDevTool);
            window.attachEvent('onmousemove', detectDevTool);
            window.attachEvent('onfocus', detectDevTool);
            window.attachEvent('onblur', detectDevTool);
            } else {
                setTimeout(argument.callee, 0);
            }
        } else {
            window.addEventListener('load', detectDevTool);
            window.addEventListener('resize', detectDevTool);
            window.addEventListener('mousemove', detectDevTool);
            window.addEventListener('focus', detectDevTool);
            window.addEventListener('blur', detectDevTool);
        }
    }();
    @endif
</script>
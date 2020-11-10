<!doctype html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="/css/bootstrap.min.css" type="text/css" media="screen" />

        <script type="text/javascript" src="/js/cleave.min.js"></script>
        <script type="text/javascript" src="/js/cleave-phone.us.js" defer></script>
        <script type="text/javascript" src="/js/jquery.min.js"></script>
        <script type="text/javascript" src="/js/bootstrap.bundle.min.js" defer></script>
        <script type="text/javascript" src="/js/jquery.form.min.js" defer></script>
        <style>
         html {
             padding: 0.5rem;
         }
        </style>

        <meta charset="UTF-8"/>
        <title>Submission Form</title>
    </head>
    <body>
        <h1>Subscription form</h1>
        <form id="myForm" action="" method="POST" data-no-instant>
            <div class="form-row">
                <div class="form-group col-12 col-lg-6 col-xl-4">
                    <label for="subscriberName">Your name</label>
                    <input id="subscriberName"
                           class="form-control"
                           name="subscriberName"
                           type="text"
                           placeholder="Enter your name"
                           required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-12 col-md-6 col-xl-4">
                    <label for="subscriberPhone">Telephone number</label>
                    <input  id="subscriberPhone"
                            class="form-control input-phone"
                            name="subscriberPhone"
                            type="tel"
                            placeholder="Enter your phone number"
                            required>
                    <small id="emailHelp" class="form-text text-muted">Enter your ten-digit US telephone number.</small>
                </div>
                <div class="form-group col-12 col-md-6 col-xl-4">
                    <label for="subscriberEmail">Email address</label>
                    <input id="subscriberEmail"
                           class="form-control"
                           name="subscriberEmail"
                           type="email"
                           placeholder="Enter your email"
                           required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-12 col-xl-8">
                    <label for="formControlTextarea">Message</label>
                    <textarea id="subscriberMessage"
                              class="form-control"
                              name="subscriberMessage"
                              rows="5"
                              placeholder="Enter your message"
                              required></textarea>
                    <small class="form-text text-muted">Briefly, explain why you'd like to join our cult.</small>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <span class="spinner-ph"></span>
                Submit
            </button>
            <button type="reset" class="btn btn-secondary">Reset</button>

        </form>
        <div id="formResults"></div>
    </body>

    <script>
     $(function() {

         const app = (function bar() {
             var cleave = new Cleave(".input-phone", {
                 phone: true,
                 phoneRegionCode: "US",
             })

             $("form#myForm").on("submit", function(e) {
                 e.preventDefault()
                 const data = $(this).serializeArray();
                 $.post("/process", data, function(d) {
                    console.log(d); 
                 });

             });
             return bar
         });
         app()
     });
    </script>
</html>

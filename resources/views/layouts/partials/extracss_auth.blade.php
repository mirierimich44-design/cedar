   <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        /* ================================
           BACKGROUND OPTIONS
           ================================ */
         /* Note: Only uncomment one of the options below. */
        /* OPTION 1: Background Color/Gradient */
        /* Uncomment below for color background */
        
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #064e3b 0%, #065f46 40%, #0f766e 100%);
        }
        /* ── Auth page input fix: ensure text is always visible ── */
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="date"],
        textarea,
        select,
        .form-control {
            background-color: #ffffff !important;
            color: #111827 !important;
            border: 1.5px solid #d1d5db !important;
        }
        input::placeholder,
        textarea::placeholder {
            color: #9ca3af !important;
        }
        /* Tailwind transparent inputs on login page */
        input.tw-bg-transparent {
            background-color: #f9fafb !important;
            color: #111827 !important;
        }
       
        
        /* OPTION 2: Background Image */
        /* Uncomment below for image background */
        /* html {
            height: 100%;
            background-image: url('{{ asset('img/bg-image.jpg') }}');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
        } */
        
        /* Common Body Styles (Always Keep This) */
        body {
            min-height: 100vh;
            background: transparent;
            margin: 0;
            padding: 0;
        }

        h1 {
            color: #fff;
        }
    </style>

    <style type="text/css">
        /*
      * Pattern lock css
      * Pattern direction
      * http://ignitersworld.com/lab/patternLock.html
      */
        .patt-wrap {
            z-index: 10;
        }

        .patt-circ.hovered {
            background-color: #cde2f2;
            border: none;
        }

        .patt-circ.hovered .patt-dots {
            display: none;
        }

        .patt-circ.dir {
            background-image: url("http://pos.test/img/pattern-directionicon-arrow.png");
            background-position: center;
            background-repeat: no-repeat;
        }

        .patt-circ.e {
            -webkit-transform: rotate(0);
            transform: rotate(0);
        }

        .patt-circ.s-e {
            -webkit-transform: rotate(45deg);
            transform: rotate(45deg);
        }

        .patt-circ.s {
            -webkit-transform: rotate(90deg);
            transform: rotate(90deg);
        }

        .patt-circ.s-w {
            -webkit-transform: rotate(135deg);
            transform: rotate(135deg);
        }

        .patt-circ.w {
            -webkit-transform: rotate(180deg);
            transform: rotate(180deg);
        }

        .patt-circ.n-w {
            -webkit-transform: rotate(225deg);
            transform: rotate(225deg);
        }

        .patt-circ.n {
            -webkit-transform: rotate(270deg);
            transform: rotate(270deg);
        }

        .patt-circ.n-e {
            -webkit-transform: rotate(315deg);
            transform: rotate(315deg);
        }
    </style>
    <style>
        h1 {
            color: #fff;
        }
    </style>
    <style>
        .action-link[data-v-1552a5b6] {
            cursor: pointer;
        }
    </style>
    <style>
        .action-link[data-v-397d14ca] {
            cursor: pointer;
        }
    </style>
    <style>
        .action-link[data-v-49962cc0] {
            cursor: pointer;
        }
    </style>

<link href="{{ asset('css/tailwind/app.css') }}" rel="stylesheet">

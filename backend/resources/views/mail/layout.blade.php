<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $subject }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');

        body { margin:0; padding:0; background:#efefef; -webkit-font-smoothing:antialiased; }

        .preheader { display:none; max-height:0; overflow:hidden; font-size:1px; line-height:1px; color:#efefef; }

        .wrap-outer { background:#efefef; min-height:100vh; }
        .wrap-outer-td { padding:40px 16px 48px; }

        .wrap-inner { max-width:560px; width:100%; }

        .email-header { background:#2e5c45; border-radius:8px 8px 0 0; padding:22px 32px; }
        .email-header__brand {
            font-family:'Courier New',Courier,monospace;
            font-size:14px; font-weight:700;
            color:#ffffff;
            letter-spacing:0.08em; text-transform:uppercase;
        }
        .email-header__domain {
            font-family:'Courier New',Courier,monospace;
            font-size:10px;
            color:#ffffff;
            letter-spacing:0.06em; text-transform:uppercase;
            text-decoration:none;
        }

        .email-body { background:#ffffff; padding:36px 32px 40px; border-left:1px solid #e2e2e2; border-right:1px solid #e2e2e2; }

        .email-footer {
            background:#f7f7f7;
            border:1px solid #e2e2e2; border-top:none;
            border-radius:0 0 8px 8px;
            padding:20px 32px;
            text-align:center;
        }
        .email-footer__tagline {
            margin:0 0 6px;
            font-family:Arial,Helvetica,sans-serif;
            font-size:11px; color:#aaaaaa; letter-spacing:0.04em;
        }
        .email-footer__note {
            margin:0;
            font-family:Arial,Helvetica,sans-serif;
            font-size:11px; color:#cccccc;
        }
        .email-footer__link { color:#8f2f2f; text-decoration:none; }
    </style>
</head>
<body>

    {{-- Hidden preheader --}}
    <div class="preheader">
        @yield('preheader', $subject)&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌
    </div>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" class="wrap-outer">
        <tr>
            <td align="center" class="wrap-outer-td">

                <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" class="wrap-inner">

                    {{-- ── Header ── --}}
                    <tr>
                        <td class="email-header">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation">
                                <tr>
                                    <td>
                                        <span class="email-header__brand">SQL Designer</span>
                                    </td>
                                    <td align="right">
                                        <a href="https://sql-designer.com" class="email-header__domain" style="color:#ffffff;text-decoration:none;">sql-designer.com</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ── Body ── --}}
                    <tr>
                        <td class="email-body">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- ── Footer ── --}}
                    <tr>
                        <td class="email-footer">
                            <p class="email-footer__tagline">Free MySQL &amp; PostgreSQL Database Designer</p>
                            <p class="email-footer__note">
                                You&rsquo;re receiving this email because you have an account at
                                <a href="https://sql-designer.com" class="email-footer__link" style="color:#8f2f2f;text-decoration:none;">sql-designer.com</a>.
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>

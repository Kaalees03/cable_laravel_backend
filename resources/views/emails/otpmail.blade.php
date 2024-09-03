<div
    style="
        box-sizing: border-box;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica,
            Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji' !important;
        line-height: 1.6;
        color: rgb(210, 216, 223) !important;
        background-color: rgb(38, 38, 38) !important;
        margin: 0px;
        padding-bottom: 30px;
        border-radius: 10px;
    "
>
    <table
        width="100%"
        style="
            box-sizing: border-box;
            border-spacing: 0px;
            border-collapse: collapse;
            max-width: 600px;
            margin-right: auto;
            margin-left: auto;
            width: 100% !important;
            color: rgb(210, 216, 223) !important;
        "
    >
        <tbody style="color: rgb(210, 216, 223) !important">
            <tr
                style="
                    box-sizing: border-box;
                    color: rgb(210, 216, 223) !important;
                "
            >
                <td
                    style="
                        box-sizing: border-box;
                        padding-top: 20px;
                        padding-bottom: 10px;
                        color: rgb(210, 216, 223) !important;
                    "
                >
                    <h2
                        style="
                            box-sizing: border-box;
                            margin-bottom: 0px;
                            font-size: 24px;
                            text-align: start;
                            margin-top: 8px !important;
                            font-weight: 600 !important;
                            color: rgb(210, 216, 223) !important;
                        "
                    >
                        Dear {{ $mail_details['user_name'] }},
                    </h2>
                </td>
            </tr>
        </tbody>
        <tbody
            style="
                box-sizing: border-box;
                padding: 10px;
                border: 1px solid rgb(58, 157, 80);
                border-radius: 6px !important;
                display: block !important;
                color: rgb(210, 216, 223) !important;
            "
        >
            <tr>
                <td
                    style="
                        box-sizing: border-box;
                        padding: 10px;
                        border-radius: 6px !important;
                        display: block !important;
                        color: rgb(210, 216, 223) !important;
                    "
                >
                    We received a request to access your {{ env('APP_NAME') }}
                    account {{ $mail_details['email'] }} through your
                    email address.
                </td>
            </tr>
            <tr>
                <td
                    style="
                        box-sizing: border-box;
                        color: rgb(38, 38, 38) !important;
                        text-decoration: none;
                        position: relative;
                        margin: 10px;
                        display: inline-block;
                        font-size: inherit;
                        font-weight: 500;
                        white-space: nowrap;
                        vertical-align: middle;
                        cursor: pointer;
                        border-radius: 0.5em;
                        box-shadow: rgba(27, 31, 35, 0.1) 0px 1px 0px,
                            rgba(255, 255, 255, 0.03) 0px 1px 0px inset;
                        padding: 0.75em 1.5em;
                        border: 1px solid rgb(31, 136, 61);
                        background-color: rgb(58, 157, 80) !important;
                    "
                >
                    OTP Verification Code:
                    <span style="color: #ffffff"
                        >{{ $mail_details['otp_code'] }}</span
                    >
                </td>
            </tr>
        </tbody>
        <tbody>
            <tr>
                <!-- <td
                    align="center"
                    style="
                        box-sizing: border-box;
                        padding-top: 20px;
                        font-size: 13px !important;
                        color: rgb(210, 216, 223) !important;
                    "
                >
                    <a
                        href="https://homegrowbackend.wofxy.com/terms-condition"
                        class="x_157809800d-inline-block"
                        style="
                            background-color: transparent;
                            box-sizing: border-box;
                            color: rgb(93, 145, 255) !important;
                            text-decoration: none;
                            display: inline-block !important;
                        "
                        >Terms & Condition</a
                    >
                    ・
                    <a
                        href="https://homegrowbackend.wofxy.com/privacy-policy"
                        class="x_157809800d-inline-block"
                        style="
                            background-color: transparent;
                            box-sizing: border-box;
                            color: rgb(93, 145, 255) !important;
                            text-decoration: none;
                            display: inline-block !important;
                        "
                        >Privacy</a
                    >
                    ・
                    <a
                        href="https://homegrowbackend.wofxy.com/shipping-policy"
                        class="x_157809800d-inline-block"
                        style="
                            background-color: transparent;
                            box-sizing: border-box;
                            color: rgb(93, 145, 255) !important;
                            text-decoration: none;
                            display: inline-block !important;
                        "
                        >Shipping</a
                    >
                    ・
                    <a
                        href="https://homegrowbackend.wofxy.com/replacement-policy"
                        class="x_157809800d-inline-block"
                        style="
                            background-color: transparent;
                            box-sizing: border-box;
                            color: rgb(93, 145, 255) !important;
                            text-decoration: none;
                            display: inline-block !important;
                        "
                        >Replacement and Return</a
                    >
                    ・
                    <a
                        href="https://homegrowbackend.wofxy.com/"
                        class="x_157809800d-inline-block"
                        style="
                            background-color: transparent;
                            box-sizing: border-box;
                            color: rgb(93, 145, 255) !important;
                            text-decoration: none;
                            display: inline-block !important;
                        "
                        >Sign in to {{ env('APP_NAME') }}</a
                    >
                </td> -->
            </tr>
           <tr>
                <td
                    align="center"
                    style="
                        box-sizing: border-box;
                        padding-top: 10px;
                        font-size: 14px !important;
                        color: rgb(134, 144, 154) !important;
                    "
                >
                    <span
                        style="
                            font-weight: bold;
                            color: rgb(255, 255, 255) !important;
                        "
                        >Note:
                    </span>
                    This OTP is valid for 2 min only. Please do not share this
                    One Time Password with anyone.
                </td>
            </tr>
            <tr>
                <td
                    align="center"
                    style="
                        box-sizing: border-box;
                        padding-top: 10px;
                        font-size: 14px !important;
                        color: rgb(134, 144, 154) !important;
                    "
                >
                    You`re receiving this email because you send request access,
                    to change the password of {{ env('APP_NAME') }} account or
                    request to re-send the access code. If this wasn`t you,
                    please ignore this email.
                </td>
            </tr>
            <tr>
                <td
                    align="center"
                    style="
                        box-sizing: border-box;
                        font-size: 14px !important;
                        padding-top: 10px;
                        color: rgb(134, 144, 154) !important;
                    "
                >
                    
                </td>
            </tr> 
        </tbody>
    </table>
</div>

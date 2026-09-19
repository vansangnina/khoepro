<table align="center" bgcolor="#dcf0f8" border="0" cellpadding="0" cellspacing="0" style="margin:0;padding:0;background-color:#f2f2f2;width:100%!important;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;line-height:18px" width="100%">
    <tbody>
        <?php include LIBRARIES . "sample/mail/layout/header_en.php"; ?>
        <tr>
            <td align="center" style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;line-height:18px;font-weight:normal" valign="top">
                <table border="0" cellpadding="0" cellspacing="0" width="600">
                    <tbody>
                        <tr style="background:#fff">
                            <td align="left" height="auto" style="padding:15px" width="600">
                                <table width="100%">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <h1 style="font-size:17px;font-weight:bold;color:#444;padding:0 0 5px 0;margin:0">Thank you for ordering at {emailCompanyWebsite}</h1>
                                                <p style="margin:4px 0;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;line-height:18px;font-weight:normal">We are happy to announce your order #{emailOrderCode} Yours has been received and is in the process of processing. {emailCompanyWebsite} We will notify you as soon as the goods are ready to be delivered.</p>
                                                <h3 style="font-size:13px;font-weight:bold;color:{emailColor};text-transform:uppercase;margin:20px 0 0 0;padding: 0 0 5px;border-bottom:1px solid #ddd">Information line #{emailOrderCode} <span style="font-size:12px;color:#777;text-transform:none;font-weight:normal">({emailDateSend}).')</span></h3>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;line-height:18px">
                                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th align="left" style="padding:6px 9px 0px 0px;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;font-weight:bold" width="50%">Billing Information</th>
                                                            <th align="left" style="padding:6px 0px 0px 9px;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;font-weight:bold" width="50%">Delivery address</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td style="padding:3px 9px 9px 0px;border-top:0;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;line-height:18px;font-weight:normal" valign="top"><span style="text-transform:capitalize">{emailOrderInfoFullname}</span><br>
                                                                <a href="mailto:{emailOrderInfoEmail}" target="_blank">{emailOrderInfoEmail}</a><br>
                                                                {emailOrderInfoPhone}
                                                            </td>
                                                            <td style="padding:3px 0px 9px 9px;border-top:0;border-left:0;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;line-height:18px;font-weight:normal" valign="top"><span style="text-transform:capitalize">{emailOrderInfoFullname}</span><br>
                                                                <a href="mailto:{emailOrderInfoEmail}" target="_blank">{emailOrderInfoEmail}</a><br>
                                                                {emailOrderInfoAddress}<br>
                                                                Tel: {emailOrderInfoPhone}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" style="padding:7px 0px 0px 0px;border-top:0;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444" valign="top">
                                                                <p style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;line-height:18px;font-weight:normal"><strong>Payments: </strong> {emailOrderPayment}

                                                                    <?php if (!empty($params['shipPrice'])) { ?>
                                                                        <br><strong>Transport fee: </strong> {emailOrderShipPrice}
                                                                    <?php } ?>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p style="margin:4px 0;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;line-height:18px;font-weight:normal"><strong>Other requirements:</strong> <i>{emailOrderInfoRequirements}</i></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h2 style="text-align:left;margin:10px 0;border-bottom:1px solid #ddd;padding-bottom:5px;font-size:13px;color:{emailColor}">ORDER DETAILS</h2>

                                                <table border="0" cellpadding="0" cellspacing="0" style="background:#f5f5f5" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th align="left" bgcolor="{emailColor}" style="padding:6px 9px;color:#fff;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:14px">Product</th>
                                                            <th align="left" bgcolor="{emailColor}" style="padding:6px 9px;color:#fff;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:14px">Unit price</th>
                                                            <th align="center" bgcolor="{emailColor}" style="padding:6px 9px;color:#fff;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:14px;min-width:55px;">Quantity</th>
                                                            <th align="right" bgcolor="{emailColor}" style="padding:6px 9px;color:#fff;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:14px">Temporary total</th>
                                                        </tr>
                                                    </thead>
                                                    {emailOrderDetails}
                                                </table>
                                                <div style="margin:auto;text-align:center"><a href="{emailHome}" style="display:inline-block;text-decoration:none;background-color:{emailColor}!important;text-align:center;border-radius:3px;color:#fff;padding:5px 10px;font-size:12px;font-weight:bold;margin-top:5px" target="_blank">Order details at {emailCompanyWebsite}</a></div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <?php include LIBRARIES . "sample/mail/layout/footer_en.php"; ?>
    </tbody>
</table>
<tfoot style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#444;line-height:18px">
    <tr>
        <td align="right" colspan="3" style="padding:5px 9px">Provisional</td>
        <td align="right" style="padding:5px 9px"><span>{orderTempPrice}</span></td>
    </tr>

    <?php if (!empty($params['shipPrice'])) { ?>
        <tr>
            <td align="right" colspan="3" style="padding:5px 9px">Transport fee</td>
            <td align="right" style="padding:5px 9px"><span>{orderShipPrice}</span></td>
        </tr>
    <?php } ?>

    <tr bgcolor="#eee">
        <td align="right" colspan="3" style="padding:7px 9px"><strong><big>Total order value</big> </strong></td>
        <td align="right" style="padding:7px 9px"><strong><big><span>{orderTotalPrice}</span> </big> </strong></td>
    </tr>
</tfoot>
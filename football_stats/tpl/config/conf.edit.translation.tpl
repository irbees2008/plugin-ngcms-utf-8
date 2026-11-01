<form action="?mod=football_stats&amp;action=save_edit_translation" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="{id}" />
    <input type="hidden" name="cid" value="{cid}" />
    <input type="hidden" name="id_translation" value="{id_translation}" />
    <input type="hidden" name="follow_action" value="{follow_action}" />
    <table style="width: 100%">
        <tr style="background: #ebebeb;">
            <td class="contentEntry1" align="center" style="background: #ebebeb;">
                <select style="width:50px;" name="mico" class="micos" value="" />{micos}</select>
            </td>
            <td class="contentEntry1" align="center" style="background: #ebebeb;">
                <input type="text" name="minute_translation" placeholder="хвилина" value="{minute}" style="width: 42px;" />
            </td>
            <td class="contentEntry1" align="center" style="background: #ebebeb;">
                <input type="text" name="minute_translation_add" placeholder="хвилина" value="{minute_add}" style="width: 42px;" />
            </td>
            <td class="contentEntry1" align="center" style="background: #ebebeb;">
                <textarea name="text_translation" id="text_translation" placeholder="Впишіть подію..." style="width: 350px; height: 50px;">{text_translation}</textarea>
            </td>
            <td class="contentEntry1" align="center" style="background: #ebebeb;">
                <input type="file" name="file{index}" onchange="paste_img(this.value, 'src_file{index}')" />
                <input type="hidden" name="src_file{index}" id="src_file{index}" value="{src_file}" />
                <div style="margin-top: 10px">{photo}</div>
            </td>
            <td class="contentEntry1" align="center" style="background: #ebebeb;">
                <textarea name="video_for_translation" placeholder="Приклад: {l_fs:iframe}" style="width: 350px; height: 50px;">{video_for_translation}</textarea>
            </td>
            <td style="background: #ebebeb;">
                <input type="submit" name="save" value="{l_fs:save}" class="button" /><br/><br/>
                <input type="submit" name="delete" value="{l_fs:delete}" class="button" onclick="if(confirm('Delete this item on {minute} minute ?')){return true;}else{return false;}" />
            </td>
        </tr>
    </table>
</form>
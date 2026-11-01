<tr>
    <td width="100%" class="contentHead" colspan="2">
        <img src="{admin_url}/skins/default/images/nav.gif" hspace="8" alt="" />{edit_heading}
    </td>
</tr>
<tr>
    <td width="100%" class="contentEntry1">
        <table>
            <tr>
                <td>{edit_delete}</td>
                <td><input type="checkbox" name="nvdelete" /></td>
            </tr>
            <tr>
                <td>{edit_question}</td>
                <td><input type="text" style="width: 300px;" name="nvtitle" value="{title}" /></td>
            </tr>
            <tr>
                <td>{edit_answers}</td>
                <td>{answers}<br/>{edit_add_answers}<br/><textarea style="height: 30px; width: 300px;" name="nvanswers"></textarea></td>
            </tr>
            <tr>
                <td>{edit_update_answers}</td>
                <td><input type="checkbox" name="nvupdate" /></td>
            </tr>
            <tr>
                <td>{edit_access}</td>
                <td>
                    <input type="checkbox" name="nvpu" value="1"{selvu}/> {edit_registered}<br/>
                    <input type="checkbox" name="nvpg" value="1"{selvg}/> {edit_guests}
                </td>
            </tr>
            <tr>
                <td>{edit_reset}</td>
                <td><input type="checkbox" name="nvrefresh" /></td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="hidden" name="voteid" value="{voteid}" />
                    <input type="hidden" name="mode" value="editv" />
                </td>
            </tr>
        </table>
    </td>
</tr>

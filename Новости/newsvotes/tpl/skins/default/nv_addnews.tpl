<tr>
    <td width="100%" class="contentHead" colspan="2">
        <img src="{admin_url}/skins/default/images/nav.gif" hspace="8" alt="" />{form_heading}
    </td>
</tr>
<tr>
    <td width="100%" class="contentEntry1">
        <table>
            <tr>
                <td>{form_enable}</td>
                <td><input type="checkbox" name="nvenable" /></td>
            </tr>
            <tr>
                <td>{form_question}</td>
                <td><input type="text" style="width: 300px;" name="nvtitle" value="{form_default_question}" /></td>
            </tr>
            <tr>
                <td>{form_answers}<br/>({form_answers_hint})</td>
                <td><textarea style="height: 70px; width: 300px;" name="nvanswers">{form_default_answers}</textarea></td>
            </tr>
            <tr>
                <td>{form_access}</td>
                <td>
                    <input type="checkbox" name="nvpu" value="1" /> {form_registered}<br/>
                    <input type="checkbox" name="nvpg" value="1" /> {form_guests}
                </td>
            </tr>
            <tr>
                <td colspan="2"><input type="hidden" name="mode" value="addv" /></td>
            </tr>
        </table>
    </td>
</tr>

[error]<br/><div class='container'><div class='right-catalog' style='width:100% !important;margin-left: 0;'><div class='msg layout-highlight layout-highlight-msg'><div class='error'><span class='icon_error'></span><span class='text-el'>{l_login.error}</span></div></div></div></div>[/error]
[banned]<br/><div class='container'><div class='right-catalog' style='width:100% !important;margin-left: 0;'><div class='msg layout-highlight layout-highlight-msg'><div class='error'><span class='icon_error'></span><span class='text-el'>{l_login.banned}</span></div></div></div></div>[/banned]
[need.activate]<br/><div class='container'><div class='right-catalog' style='width:100% !important;margin-left: 0;'><div class='msg layout-highlight layout-highlight-msg'><div class='info'><span class='icon_info'></span><span class='text-el'>{l_login.need.activate}</span></div></div></div></div>[/need.activate]

<div class="frame-inside page-register">
    
    <div class="container">
        
        <div class="f-s_0 title-register without-crumbs">
            <div class="frame-title">
                <h1 class="title">{l_login.title}</h1>
            </div>
        </div>
        
        <div class="frame-register">
            <form name="login" method="post" action="{form_action}">
                <input type="hidden" name="redirect" value="{redirect}"/>
                <div class="horizontal-form">
                <label>
                    <span class="title">Логин:</span>
                    <div class="frame-form-field">
                        <input type="text" type="text" name="username" class="input">
                    </div>
                </label>
                <label>
                    <span class="title">Пароль:</span>
                    <div class="frame-form-field">
                        <input type="password" type="password" name="password" class="input">
                    </div>
                </label>

                <div class="frame-label">
                    <span class="title">&nbsp;</span>
                    <div class="frame-form-field">
                        <div class="btn-form m-b_15">
                            <input type="submit" value="Войти">
                        </div>
                    </div>
                </div>
                
                </div>
            </form>
        </div>
    
    </div>

</div>

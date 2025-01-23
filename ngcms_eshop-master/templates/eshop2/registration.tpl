<div class="frame-inside page-register">
    
    <div class="container">
        
        <div class="f-s_0 title-register without-crumbs">
            <div class="frame-title">
                <h1 class="title">Регистрация</h1>
            </div>
        </div>
        
        <div class="frame-register">
            <form name="register" action="{{ form_action }}" method="post">
                <input type="hidden" name="type" value="doregister" />
                <div class="horizontal-form">
                {% for entry in entries %}
                <label>
                    <span class="title">{{ entry.title }}:</span>
                    <div class="frame-form-field">
                        {{ entry.input }}
                    </div>
                </label>
                {% endfor %}
                {% if flags.hasCaptcha %}
                <label>
                    <span class="title">Введите код безопасности:</span>
                    <div class="frame-form-field">
                        <input id="reg_capcha" type="text" name="vcode" class="input">
                        <img src="{{ admin_url }}/captcha.php" onclick="reload_captcha();" id="img_captcha" style="cursor: pointer;" alt="Security code"/>
                    </div>
                </label>
                {% endif %}

                <div class="frame-label">
                    <span class="title">&nbsp;</span>
                    <div class="frame-form-field">
                    <div class="btn-form m-b_15">
                        <input type="submit" value="Зарегистрироваться">
                    </div>
                    <p class="help-block">Я уже зарегистрирован</p>
                    <ul class="items items-register-add-ref">
                        <li>
                            <a href="{{home}}/login/">
                                <button type="button">
                                <span class="text-el d_l_1">Войти</span>
                                </button>
                            </a>
                        </li>
                        <li>
                        <span class="divider">|</span>
                            <a href="{{home}}/lostpassword/">
                                <button type="button">
                                <span class="text-el d_l_1">Напомнить пароль</span>
                                </button>
                            </a>
                        </li>
                    </ul>
                    </div>
                </div>
                
                </div>
            </form>
        </div>
    
    </div>

</div>

<script src="{{ tpl_url }}/js/registration.js"></script>

<script type="text/javascript">

	function reload_captcha() {
		var captc = document.getElementById('img_captcha');
		if (captc != null) {
			captc.src = "{{ admin_url }}/captcha.php?rand="+Math.random();
		}
	}   
</script>

    <div class="card">
        <h2 style="margin-top:0;font-size:1rem">Подключение</h2>
        <p class="muted" style="margin-bottom:.4rem">Нажмите <b>?</b> у любого поля — справа откроется справка с примером. Формат: <b>домены — без</b> <code>https://</code>, а <b>URL панели — со схемой</b> <code>https://</code>.</p>
        <?php if (submw_in_docker()): ?><div class="info" style="margin-bottom:.6rem">Docker-режим: источник «Панель» и адреса панели/subpage заданы окружением контейнера (compose) и здесь только для чтения. Достаточно задать <b>API-токен панели</b>.</div><?php endif; ?>
        <form method="post" data-autosave>
            <input type="hidden" name="csrf" value="<?= h($token) ?>">
            <input type="hidden" name="action" value="save_connection">
            <div class="row">
                <div><label>Origin — домен подписки <button type="button" class="qh" onclick="help('origin')" aria-label="Справка">?</button></label><input type="text" name="target_domain" value="<?= h(target_domain()) ?>" placeholder="sub.example.com"></div>
                <div><label>Домен зеркала <button type="button" class="qh" onclick="help('mirror')" aria-label="Справка">?</button></label><input type="text" name="mirror_domain" value="<?= h($mirror) ?>" placeholder="mirror.example.com"></div>
            </div>
            <div class="row">
                <div><label>URL панели Remnawave <button type="button" class="qh" onclick="help('rwurl')" aria-label="Справка">?</button></label><input type="text" name="remnawave_url" value="<?= h(remnawave_url()) ?>" placeholder="https://panel.example.com" <?= submw_in_docker() ? 'readonly' : '' ?>></div>
                <div><label>Cookie панели (eGames-защита) <button type="button" class="qh" onclick="help('cookie')" aria-label="Справка">?</button></label><input type="text" name="remnawave_cookie" value="<?= h(remnawave_cookie()) ?>" placeholder="aB3xK9pQ=Zt7mW2nR"></div>
            </div>
            <div class="row">
                <div><label>API-токен панели <button type="button" class="qh" onclick="help('apikey')" aria-label="Справка">?</button></label><input type="password" name="remnawave_api_key" value="" placeholder="<?= remnawave_token() ? '•••••• задан' : 'не задан' ?>"></div>
                <div><label>Секрет вебхука <button type="button" class="qh" onclick="help('whsecret')" aria-label="Справка">?</button></label><input type="password" name="webhook_secret" value="" placeholder="<?= webhook_secret() ? '•••••• задан' : 'не задан' ?>"></div>
            </div>
            <div class="set-row">
                <div class="set-info"><div class="set-t">Таймаут проксирования, сек <button type="button" class="qh" onclick="help('timeout')" aria-label="Справка">?</button></div><div class="set-d">Сколько ждать ответа origin при запросе подписки.</div></div>
                <input type="number" name="proxy_timeout" value="<?= h(proxy_timeout()) ?>">
            </div>
            <div class="set-grid2">
                <div class="set-row">
                    <div class="set-info"><div class="set-t">Доверять заголовку expire <button type="button" class="qh" onclick="help('trust')" aria-label="Справка">?</button></div><div class="set-d">Рекомендуется — продление подписки чинит себя само.</div></div>
                    <label class="switch"><input type="checkbox" name="trust_header_expire" <?= trust_header_expire()?'checked':'' ?>><span class="sl"></span></label>
                </div>
                <div class="set-row">
                    <div class="set-info"><div class="set-t">Проверять TLS-сертификат панели и origin</div><div class="set-d">Защита от MITM при запросах к панели и origin. <b>Выключение действует на все исходящие HTTPS-запросы прослойки</b> (панель, origin, доп-подписки, пересылка вебхуков, чат): любой сертификат принимается без проверки, и API-токен/cookie панели можно перехватить. Выключайте <b>только</b> при самоподписанном сертификате в доверенной сети — лучше поставьте валидный сертификат.</div></div>
                    <label class="switch"><input type="checkbox" name="tls_verify" <?= api_tls_verify()?'checked':'' ?>><span class="sl"></span></label>
                </div>
            </div>
            <div class="set-row">
                <div class="set-info"><div class="set-t">Источник подписки</div><div class="set-d">«Зеркало» — проксирование origin-домена (как раньше). «Панель» — прослойка сама становится подпиской (sub-сервис Remnawave): конфиги берутся с <code>/api/sub</code> панели, а в браузере рендерится страница подписки. Адрес панели — это контейнер/loopback (рядом с панелью) <b>или</b> публичный <code>https://</code>-домен (если прослойка на отдельном сервере).</div></div>
                <?php if (submw_in_docker()): ?><input type="hidden" name="sub_source" value="panel"><?php endif; ?>
                <select name="sub_source" <?= submw_in_docker() ? 'disabled' : '' ?>>
                    <option value="mirror" <?= sub_source()==='mirror'?'selected':'' ?>>Зеркало (origin)</option>
                    <option value="panel" <?= sub_source()==='panel'?'selected':'' ?>>Панель (sub-сервис)</option>
                </select>
            </div>
            <div class="row">
                <div><label>Адрес subscription-page (режим «Панель»)</label><input type="text" name="subpage_external_url" value="<?= h(subpage_external_url()) ?>" placeholder="https://panel.example.com или http://127.0.0.1:3010" <?= submw_in_docker() ? 'readonly' : '' ?>><div class="muted" style="font-size:.8rem;margin-top:.3rem">Рядом с панелью — адрес контейнера/loopback; на отдельном сервере — публичный https-адрес панели.</div></div>
                <div></div>
            </div>
            <?php $ua_keys_now = ua_hwid_keys(); $ua_key_meta = ['x-hwid' => 'идентификатор устройства (влияет на лимит)', 'x-device-os' => 'ОС устройства', 'x-ver-os' => 'версия ОС', 'x-device-model' => 'модель устройства']; ?>
            <div class="set-row">
                <div class="set-info"><div class="set-t">HWID из User-Agent <button type="button" class="qh" onclick="help('uahwid')" aria-label="Справка">?</button></div><div class="set-d">Если клиент (v2rayNG, Clash) не умеет слать HTTP-заголовки, но даёт менять User-Agent — извлекать device-заголовки из строки вида <code>...; x-hwid=значение; ...)</code> и форвардить на панель. Реальный заголовок всегда главнее. <b>Внимание:</b> User-Agent задаётся пользователем вручную, поэтому <code>x-hwid</code> так можно подменить — это ослабляет лимит устройств; HWID здесь удобство, не защита. Держите выключенным, если не требуется.</div></div>
                <label class="switch"><input type="checkbox" name="ua_hwid_parse" <?= ua_hwid_parse() ? 'checked' : '' ?>><span class="sl"></span></label>
            </div>
            <div class="set-row" style="display:block">
                <div class="set-info" style="margin-bottom:.65rem"><div class="set-t">Какие ключи извлекать</div><div class="set-d">Отмеченные заголовки берутся из User-Agent, когда настоящего заголовка в запросе нет.</div></div>
                <div class="uahk-grid">
                    <?php foreach ($ua_key_meta as $uk => $ud): ?>
                        <label class="uahk"><input type="checkbox" name="ua_hwid_keys[]" value="<?= h($uk) ?>" <?= in_array($uk, $ua_keys_now, true) ? 'checked' : '' ?>><span class="uahk-txt"><code><?= h($uk) ?></code><span class="muted"><?= h($ud) ?></span></span></label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div style="margin-top:1.25rem"><button type="submit">💾 Сохранить подключение</button></div>
        </form>
    </div>
    <style>
        .set-grid2{display:grid;grid-template-columns:1fr 1fr;gap:.55rem;margin-top:.7rem}
        .set-grid2 .set-row{margin-top:0}
        @media(max-width:720px){.set-grid2{grid-template-columns:1fr}}
        .uahk-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:.5rem}
        .uahk{display:flex;align-items:center;gap:.65rem;border:1px solid var(--line);background:var(--bg2);border-radius:10px;padding:.6rem .8rem;cursor:pointer;transition:border-color .15s}
        .uahk:hover{border-color:var(--accent)}
        .uahk input{appearance:none;-webkit-appearance:none;width:18px;height:18px;border:2px solid var(--line);border-radius:50%;flex:0 0 auto;margin:0;cursor:pointer;position:relative;transition:border-color .15s,background .15s}
        .uahk input:checked{border-color:var(--accent);background:var(--accent)}
        .uahk input:checked::after{content:"";position:absolute;left:50%;top:50%;width:6px;height:6px;border-radius:50%;background:var(--accent-text);transform:translate(-50%,-50%)}
        .uahk-txt{min-width:0;display:flex;flex-direction:column;gap:.1rem;line-height:1.3}
        .uahk-txt .muted{font-size:.76rem}
    </style>


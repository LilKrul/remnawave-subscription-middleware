    <section class="coll" data-coll="wgpool_help">
        <button type="button" class="coll-head" onclick="collToggle(this)"><span>Как работает пул и почему так</span>
            <span class="coll-hr"><svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></span>
        </button>
        <div class="coll-body">
            <p class="muted" style="margin-top:0">WireGuard/AmneziaWG используют публичный ключ как идентификатор пира: у пира один текущий endpoint, и при одновременной работе двух устройств с <b>одним</b> ключом endpoint перетирается на «последнего» — соединения флапают. Поэтому одно одновременное устройство = один ключ = один peer на сервере.</p>
            <p class="muted">Прослойка ключи не генерит (их делают в Amnezia / WG-панели / CLI), а <b>раздаёт</b> готовые конфиги из пула, закрепляя за подписчиком или устройством отдельный, никем больше не используемый <code>.conf</code>. Закрепление «липкое»: после импорта клиент держит ключ, пока сам не перечитает подписку.</p>
            <ul class="muted" style="margin:.2rem 0 0;padding-left:1.1rem;line-height:1.65">
                <li><b>Общий</b> — конфиги сквада дописываются всем подписчикам. Когда уникальность ключа не нужна.</li>
                <li><b>На пользователя</b> — из пула выдаётся один WG/AWG-конфиг на подписчика; hwid не нужен.</li>
                <li><b>На устройство</b> — один конфиг на пару пользователь+устройство (hwid). Клиент не прислал hwid → конфиг не подмешиваем (иначе флап).</li>
            </ul>
            <p class="muted" style="margin-bottom:0">VLESS-конфиги сквада в любом режиме отдаются всем (они без этого ограничения) — управляй ими во вкладке «Доп. конфиги». Конфигов в пуле меньше, чем нужно устройств → части подписчиков WG просто не достанется (без флапа). Смотри расчёт ниже.</p>
        </div>
    </section>

    <section class="coll" data-coll="wgpool_upload">
        <button type="button" class="coll-head" onclick="collToggle(this)"><span>Загрузка WG / AWG конфигов</span>
            <span class="coll-hr"><svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></span>
        </button>
        <div class="coll-body">
            <?php if ($sqcfg_squads_err !== ''): ?>
                <div class="warn">Список сквадов недоступен: <?= h($sqcfg_squads_err) ?>. Проверьте URL панели и токен во вкладке «Подключение».</div>
            <?php elseif (!$sqcfg_squads): ?>
                <div class="warn">Внутренние сквады не получены. Настройте подключение к панели.</div>
            <?php endif; ?>
            <p class="muted" style="margin-top:0">Пакетно: выбери <code>.conf</code>-файлы (метка возьмётся из имени файла) и/или вставь несколько конфигов подряд — они режутся по секции <code>[Interface]</code>. Тип (WG или AmneziaWG) определяется автоматически; не-WG/AWG пропускаются.</p>
            <form method="post" enctype="multipart/form-data" autocomplete="off">
                <input type="hidden" name="csrf" value="<?= h($token) ?>">
                <input type="hidden" name="action" value="batch_wg_config">
                <label>1. Сквады <span class="muted" style="font-weight:400">— применятся ко всем загружаемым конфигам</span></label>
                <input type="text" class="sq-search" placeholder="поиск сквада…">
                <div class="sq-grid">
                    <?php foreach ($sqcfg_squads as $s): ?>
                        <label class="sq-item"><input type="checkbox" name="squads[]" value="<?= h($s['uuid']) ?>"><span class="sq-n"><?= h($s['name']) ?></span><span class="muted" style="font-size:.78rem"><?= (int) $s['members'] ?></span></label>
                    <?php endforeach; ?>
                    <?php if (!$sqcfg_squads): ?><span class="muted" style="font-size:.82rem">Сквады не получены — настройте подключение.</span><?php endif; ?>
                </div>
                <div style="margin-top:1rem">
                    <label>2. Файлы .conf <span class="muted" style="font-weight:400">— можно несколько</span></label>
                    <input type="file" name="conf_files[]" accept=".conf,.txt" multiple style="display:block">
                </div>
                <div style="margin-top:1rem">
                    <label>…и/или вставка текстом</label>
                    <textarea name="raw_batch" rows="8" spellcheck="false" placeholder="[Interface]&#10;PrivateKey = …&#10;…&#10;[Peer]&#10;…&#10;&#10;[Interface]&#10;… следующий конфиг …" style="width:100%;font-family:monospace;font-size:.82rem;box-sizing:border-box"></textarea>
                </div>
                <div style="margin-top:1rem;max-width:420px">
                    <label>Префикс метки <span class="muted" style="font-weight:400">— необязательно</span></label>
                    <input type="text" name="label_prefix" class="sqcfg-flag" maxlength="120" placeholder="напр.: Нидерланды" style="width:100%;box-sizing:border-box">
                </div>
                <div style="margin-top:1rem;display:flex;align-items:center;gap:.75rem;flex-wrap:wrap">
                    <button type="submit" class="btn">Загрузить</button>
                    <span class="muted" style="font-size:.8rem">Метки потом можно переименовать в списке ниже.</span>
                </div>
            </form>
        </div>
    </section>

    <section class="coll" data-coll="wgpool_modes">
        <button type="button" class="coll-head" onclick="collToggle(this)"><span>Режим пула по сквадам</span>
            <span class="coll-hr"><svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></span>
        </button>
        <div class="coll-body">
            <p class="muted" style="margin-top:0">Как раздаются WG/AWG-конфиги каждого сквада. «Потребность» считается по панели: фактические устройства из базы hwid.</p>
            <?php $wgpT0 = $sqcfg_sizing['totals'] ?? ['records' => 0, 'unique' => 0]; ?>
            <div id="wgpTotals" class="muted" style="font-size:.82rem;margin:.2rem 0 .8rem">Всего регистраций устройств: <b id="wgpTotRec"><?= (int) $wgpT0['records'] ?></b> · уникальных hwid: <b id="wgpTotUniq"><?= (int) $wgpT0['unique'] ?></b></div>
            <?php if (!$sqcfg_squads): ?>
                <div class="warn">Сквады не получены — настройте подключение к панели.</div>
            <?php else: ?>
            <form method="post">
                <input type="hidden" name="csrf" value="<?= h($token) ?>">
                <input type="hidden" name="action" value="save_pool_modes">
                <table class="logtbl wgpool-tbl">
                    <thead><tr><th>Сквад</th><th>Режим</th><th>В пуле</th><th>Учёток: актив. / всего</th><th>Добавлено (факт)</th></tr></thead>
                    <tbody>
                    <?php foreach ($sqcfg_squads as $s): $pu = $s['uuid']; $pm = $sqcfg_modes[$pu] ?? 'shared'; ?>
                        <tr>
                            <td><?= h($s['name']) ?></td>
                            <td>
                                <select name="pool_mode[<?= h($pu) ?>]" class="sqcfg-sel">
                                    <option value="shared"<?= $pm === 'shared' ? ' selected' : '' ?>>Общий</option>
                                    <option value="users"<?= $pm === 'users' ? ' selected' : '' ?>>На пользователя</option>
                                    <option value="devices"<?= $pm === 'devices' ? ' selected' : '' ?>>На устройство</option>
                                </select>
                            </td>
                            <td><?= (int) ($sqcfg_stock[$pu] ?? 0) ?></td>
                            <td class="wgp-u" data-su="<?= h($pu) ?>">—</td>
                            <td class="wgp-d" data-su="<?= h($pu) ?>">—</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <div style="display:flex;gap:.75rem;align-items:center;flex-wrap:wrap;margin-top:1rem">
                    <button type="submit" class="btn">Сохранить режимы</button>
                    <button type="button" class="sqcfg-btn" id="wgpCalc">Рассчитать потребность</button>
                    <label class="muted" style="display:flex;align-items:center;gap:.4rem;margin:0;font-weight:400">авто-возврат слота через
                        <input type="number" name="wgpool_reclaim_days" value="<?= (int) $sqcfg_reclaim_days ?>" min="1" max="365" style="width:5rem;box-sizing:border-box"> дн. неактивности</label>
                </div>
                <div id="wgpCalcMsg" class="muted" style="font-size:.8rem;margin-top:.5rem"></div>
                <div class="muted" style="font-size:.78rem;line-height:1.6;margin-top:.7rem">
                    <b>В пуле</b> — WG/AWG-конфигов (слотов) в пуле сквада. <b>Учёток</b> — пользователей сквада: активных / всего. <b>Добавлено (факт)</b> — реально зарегистрировано устройств юзерами сквада, из базы hwid. Красным, если конфигов в пуле меньше факта.
                </div>
            </form>
            <?php endif; ?>
        </div>
    </section>

    <section class="coll" data-coll="wgpool_manual">
        <button type="button" class="coll-head" onclick="collToggle(this)"><span>Ручная привязка конфига</span>
            <span class="coll-hr"><svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></span>
        </button>
        <div class="coll-body">
            <p class="muted" style="margin-top:0">Жёстко закрепить конкретный конфиг за пользователем и, по желанию, за конкретным устройством. Такая привязка приоритетнее авто-выдачи и не возвращается в пул. <b>Выбор устройства не обязателен</b> — без него конфиг закрепляется за пользователем целиком.</p>
            <form method="post" autocomplete="off">
                <input type="hidden" name="csrf" value="<?= h($token) ?>">
                <input type="hidden" name="action" value="pool_manual_add">
                <input type="hidden" name="short_uuid" id="wgm_short">
                <div class="sqcfg-grid">
                    <div>
                        <label>Сквад (пул)</label>
                        <select name="pool_squad" id="wgm_squad" class="sqcfg-sel">
                            <option value="">—</option>
                            <?php foreach ($sqcfg_squads as $s): ?><option value="<?= h($s['uuid']) ?>"><?= h($s['name']) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label>Конфиг из пула</label>
                        <select name="config_id" id="wgm_cfg" class="sqcfg-sel"><option value="">—</option></select>
                    </div>
                    <div>
                        <label>Пользователь (shortUuid или имя)</label>
                        <div style="display:flex;gap:.4rem"><input type="text" id="wgm_q" placeholder="shortUuid / username" style="flex:1;box-sizing:border-box"><button type="button" class="sqcfg-btn" id="wgm_find">Найти</button></div>
                    </div>
                    <div>
                        <label>Устройство (необязательно)</label>
                        <select id="wgm_hwid" name="hwid" class="sqcfg-sel"><option value="">— любое (на пользователя)</option></select>
                    </div>
                </div>
                <div id="wgm_info" class="muted" style="font-size:.82rem;margin:.6rem 0"></div>
                <button type="submit" class="btn" id="wgm_submit" disabled>Привязать</button>
            </form>
            <?php $man = array_values(array_filter($sqcfg_leases, fn($l) => (int) $l['manual'] === 1)); ?>
            <h2 style="font-size:.95rem;margin:1.3rem 0 .5rem">Текущие привязки (<?= count($man) ?>)</h2>
            <?php if (!$man): ?><p class="muted">Пока пусто.</p><?php else: ?>
            <table class="logtbl">
                <thead><tr><th>Сквад</th><th>Конфиг</th><th>Пользователь</th><th>Устройство</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($man as $l): $lcid = (int) $l['config_id']; $lcn = ''; foreach ($sqcfg_wg as $cc) { if ((int) $cc['id'] === $lcid) { $lcn = (string) ($cc['name'] ?? ''); break; } } ?>
                    <tr>
                        <td><?= h($sqcfg_names[$l['pool_id']] ?? $l['pool_id']) ?></td>
                        <td><?= $lcn !== '' ? h($lcn) : ('#' . $lcid) ?></td>
                        <td style="font-family:monospace;font-size:.78rem"><?= h((string) $l['short_uuid']) ?></td>
                        <td style="font-family:monospace;font-size:.76rem"><?= $l['hwid'] !== null && $l['hwid'] !== '' ? h((string) $l['hwid']) : '<span class="muted">любое</span>' ?></td>
                        <td style="text-align:right">
                            <form method="post" style="margin:0" onsubmit="return uiConfirmForm(this,'Снять привязку?')">
                                <input type="hidden" name="csrf" value="<?= h($token) ?>">
                                <input type="hidden" name="action" value="pool_manual_del">
                                <input type="hidden" name="id" value="<?= (int) $l['id'] ?>">
                                <button type="submit" class="danger">🗑</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </section>

    <div class="card">
        <div class="loghead">
            <h2>WG / AWG конфиги (<?= count($sqcfg_wg) ?>)</h2>
            <?php if ($sqcfg_wg): ?>
            <div class="loghead-r">
                <label class="pgr-size" style="display:inline-flex;align-items:center;gap:.4rem;margin:0;font-weight:400">На странице:
                    <select id="wgSize" onchange="SQCFGP.setSize(parseInt(this.value,10))">
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="200">200</option>
                    </select>
                </label>
            </div>
            <?php endif; ?>
        </div>
        <?php if (!$sqcfg_wg): ?>
            <p class="muted">Пока пусто. Загрузите WG/AWG-конфиги выше.</p>
        <?php else: $sqcfg_edit = []; ?>
        <table class="logtbl" id="wgTbl">
            <thead><tr><th>Сквады</th><th>Тип</th><th>Метка</th><th>Статус</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($sqcfg_wg as $c):
                $pn = json_decode((string) ($c['parsed'] ?? ''), true);
                $sumr = is_array($pn) ? squadconf_summary($pn) : ($c['type'] ?? '');
                $csquads = squadconf_squads_of($c);
                $on = (int) $c['enabled'] === 1;
                $sqcfg_edit[(int) $c['id']] = ['squads' => array_values($csquads), 'name' => (string) ($c['name'] ?? ''), 'raw' => (string) $c['raw']];
            ?>
            <tr>
                <td><?php foreach ($csquads as $sq): ?><span class="sq-tag"><?= h($sqcfg_names[$sq] ?? $sq) ?></span><?php endforeach; ?></td>
                <td><span class="tag normal"><?= h($sumr) ?></span></td>
                <td><?= $c['name'] !== null && $c['name'] !== '' ? h($c['name']) : '<span class="muted">—</span>' ?></td>
                <td>
                    <form method="post" style="margin:0;display:inline">
                        <input type="hidden" name="csrf" value="<?= h($token) ?>">
                        <input type="hidden" name="action" value="toggle_squad_config">
                        <input type="hidden" name="ret" value="wg_pool">
                        <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                        <input type="hidden" name="enabled" value="<?= $on ? '0' : '1' ?>">
                        <button type="submit" class="sqcfg-btn <?= $on ? '' : 'off' ?>"><?= $on ? '✅ Включён' : '⛔ Выключен' ?></button>
                    </form>
                </td>
                <td style="text-align:right;white-space:nowrap">
                    <button type="button" class="sqcfg-btn sqcfg-edit" data-id="<?= (int) $c['id'] ?>">✎ Изменить</button>
                    <form method="post" style="margin:0;display:inline" onsubmit="return uiConfirmForm(this,'Удалить этот конфиг?')">
                        <input type="hidden" name="csrf" value="<?= h($token) ?>">
                        <input type="hidden" name="action" value="del_squad_config">
                        <input type="hidden" name="ret" value="wg_pool">
                        <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                        <button type="submit" class="danger">🗑 Удалить</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div id="wgPager" style="margin-top:.85rem;display:flex;justify-content:flex-end"></div>
        <?php endif; ?>
    </div>

    <div id="sqEditModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-head">
                <div>Редактировать конфиг</div>
                <button type="button" class="modal-x" onclick="sqEditClose()">×</button>
            </div>
            <div class="modal-body">
                <form method="post" autocomplete="off">
                    <input type="hidden" name="csrf" value="<?= h($token) ?>">
                    <input type="hidden" name="action" value="edit_squad_config">
                    <input type="hidden" name="ret" value="wg_pool">
                    <input type="hidden" name="id" id="sqedit_id" value="">
                    <div style="margin-bottom:.85rem">
                        <label>Сквады</label>
                        <input type="text" class="sq-search" placeholder="поиск сквада…">
                        <div class="sq-grid" id="sqedit_chips">
                            <?php foreach ($sqcfg_squads as $s): ?>
                                <label class="sq-item"><input type="checkbox" name="squads[]" value="<?= h($s['uuid']) ?>"><span class="sq-n"><?= h($s['name']) ?></span><span class="muted" style="font-size:.78rem"><?= (int) $s['members'] ?></span></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div style="margin-bottom:.85rem">
                        <label>Метка</label>
                        <input type="text" name="name" id="sqedit_name" class="sqcfg-flag" maxlength="191" required style="width:100%;box-sizing:border-box">
                    </div>
                    <div style="margin-bottom:.85rem">
                        <label>Конфиг</label>
                        <textarea name="raw" id="sqedit_raw" rows="11" spellcheck="false" required style="width:100%;font-family:monospace;font-size:.82rem;box-sizing:border-box"></textarea>
                    </div>
                    <div style="display:flex;gap:.6rem">
                        <button type="submit" class="btn">Сохранить изменения</button>
                        <button type="button" class="sqcfg-btn" onclick="sqEditClose()">Отмена</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .sqcfg-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:.7rem 1rem;align-items:end}
        .sqcfg-grid select,.sqcfg-grid input{width:100%;box-sizing:border-box}
        .sqcfg-grid label{display:block;margin-bottom:.3rem;font-weight:600;font-size:.82rem}
        .sqcfg-sel{appearance:none;-webkit-appearance:none;-moz-appearance:none;padding-right:2.2rem;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23888' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right .75rem center;background-size:.95rem}
        .sqcfg-btn{background:transparent;border:1px solid var(--line);color:var(--text);border-radius:8px;padding:.4rem .75rem;font-size:.82rem;font-weight:600;cursor:pointer}
        .sqcfg-btn.off{opacity:.65}
        .sqcfg-edit{margin-right:.45rem}
        #sqEditModal label:not(.sq-item){display:block;margin-bottom:.3rem;font-weight:600;font-size:.82rem}
        .card label{display:block;margin-bottom:.35rem;font-weight:600;font-size:.85rem}
        .sq-search{width:100%;margin:.1rem 0 .55rem;box-sizing:border-box}
        .sq-tag{display:inline-block;background:var(--bg2);border:1px solid var(--line);border-radius:6px;padding:.08rem .45rem;font-size:.74rem;margin:.1rem .25rem .1rem 0;white-space:nowrap}
        .wgpool-tbl td,.wgpool-tbl th{vertical-align:middle}
        .wgpool-tbl .sqcfg-sel{padding:.3rem 2rem .3rem .6rem;font-size:.82rem}
        .wgp-warn{color:var(--c-warn-fg);font-weight:700}
    </style>
    <?php include __DIR__ . '/_sqcfg_js.php'; ?>
    <script>
    window.SQCFG = <?= json_encode($sqcfg_edit ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    sqcfgInitEdit();
    sqcfgInitPager('wgTbl', 'wgPager', 'wgSize', 'wgpool_size');
    (function(){
        var NAMES = <?= json_encode($sqcfg_names, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        var POOL = <?= json_encode($sqcfg_pool_cfgs ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        var SIZING = <?= json_encode($sqcfg_sizing['rows'] ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        var SIZING_TS = <?= (int) ($sqcfg_sizing['ts'] ?? 0) ?>;
        function wgpFmtAgo(ts){ if(!ts) return ''; var s=Math.max(0,Math.floor(Date.now()/1000-ts)); if(s<60) return 'только что'; if(s<3600) return Math.floor(s/60)+' мин назад'; if(s<86400) return Math.floor(s/3600)+' ч назад'; return Math.floor(s/86400)+' дн назад'; }
        function wgpApply(rows){
            document.querySelectorAll('.wgp-u').forEach(function(td){
                var row = td.parentNode, su = td.dataset.su, r = (rows || {})[su];
                var dd = row.querySelector('.wgp-d');
                var stock = parseInt((row.children[2] || {}).textContent || '0', 10);
                if (!r) { td.textContent = '0'; dd.textContent = '0'; dd.classList.remove('wgp-warn'); return; }
                td.textContent = (r.active || 0) + ' / ' + (r.users || 0);
                dd.textContent = r.devices || 0;
                if (stock < (r.devices || 0)) dd.classList.add('wgp-warn'); else dd.classList.remove('wgp-warn');
            });
        }
        var WGP_HINT = 'Добавлено — реально зарегистрированные устройства из базы hwid. Красным — пул меньше факта.';
        var calc = document.getElementById('wgpCalc'), wgpMsg = document.getElementById('wgpCalcMsg');
        if (SIZING && Object.keys(SIZING).length) { wgpApply(SIZING); if (wgpMsg) wgpMsg.innerHTML = 'Последний расчёт: ' + wgpFmtAgo(SIZING_TS) + '. ' + WGP_HINT; }
        if (calc) {
            calc.addEventListener('click', function(){
                if (wgpMsg) wgpMsg.textContent = 'Считаю по панели…'; calc.disabled = true;
                fetch('?ajax=pool_sizing').then(function(r){ return r.json(); }).then(function(d){
                    calc.disabled = false;
                    if (!d.ok) { if (wgpMsg) wgpMsg.textContent = 'Ошибка: ' + (d.error || 'нет данных'); return; }
                    wgpApply(d.rows);
                    if (d.totals) { var tr = document.getElementById('wgpTotRec'), tu = document.getElementById('wgpTotUniq'); if (tr) tr.textContent = d.totals.records || 0; if (tu) tu.textContent = d.totals.unique || 0; }
                    var extra = d.warn ? (' <span class="wgp-warn">hwid-эндпоинт: ' + d.warn + '</span>') : '';
                    if (wgpMsg) wgpMsg.innerHTML = 'Рассчитано только что. ' + WGP_HINT + extra;
                }).catch(function(){ calc.disabled = false; if (wgpMsg) wgpMsg.textContent = 'Ошибка запроса'; });
            });
        }
        var sqSel = document.getElementById('wgm_squad'), cfgSel = document.getElementById('wgm_cfg');
        function fillCfg(){
            if (!cfgSel) return;
            var sq = sqSel.value; cfgSel.innerHTML = '<option value="">—</option>';
            (POOL[sq] || []).forEach(function(c){
                var o = document.createElement('option'); o.value = c.id; o.textContent = (c.name || ('#' + c.id)) + ' · ' + c.type; cfgSel.appendChild(o);
            });
            chkReady();
        }
        function chkReady(){
            var btn = document.getElementById('wgm_submit'); if (!btn) return;
            btn.disabled = !(document.getElementById('wgm_short').value && cfgSel.value && sqSel.value);
        }
        if (sqSel) sqSel.addEventListener('change', fillCfg);
        if (cfgSel) cfgSel.addEventListener('change', chkReady);
        var findBtn = document.getElementById('wgm_find');
        if (findBtn) {
            findBtn.addEventListener('click', function(){
                var q = document.getElementById('wgm_q').value.trim(); if (!q) return;
                var info = document.getElementById('wgm_info'); info.textContent = 'Ищу…';
                fetch('?ajax=pool_user&q=' + encodeURIComponent(q)).then(function(r){ return r.json(); }).then(function(d){
                    var hw = document.getElementById('wgm_hwid');
                    if (!d.ok) { info.textContent = d.error || 'Не найден'; document.getElementById('wgm_short').value = ''; chkReady(); return; }
                    document.getElementById('wgm_short').value = d.user.shortUuid || '';
                    var sqn = (d.user.squads || []).map(function(s){ return NAMES[s.uuid] || s.name || s.uuid; }).join(', ');
                    info.innerHTML = 'Пользователь: <b>' + (d.user.username || '') + '</b> · лимит устройств: ' + (d.user.hwidDeviceLimit == null ? 'по умолчанию' : d.user.hwidDeviceLimit) + (sqn ? (' · сквады: ' + sqn) : '');
                    hw.innerHTML = '<option value="">— любое (на пользователя)</option>';
                    (d.devices || []).forEach(function(dv){
                        var o = document.createElement('option'); o.value = dv.hwid; o.textContent = (dv.platform || dv.deviceModel || '') + ' · ' + (dv.hwid || ''); hw.appendChild(o);
                    });
                    chkReady();
                }).catch(function(){ info.textContent = 'Ошибка запроса'; });
            });
        }
    })();
    </script>

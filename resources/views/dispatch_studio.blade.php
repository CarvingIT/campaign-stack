@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
<script src="/js/jquery.min.js"></script>

<script>
    let currentAudienceData = [];
    let selectedContactIds = new Set();
    let currentNewsletterId = {{ $selectedNewsletter->id ?? 'null' }};
    let isFlushingActive = false;
    let currentStage = 1;
    let maxUnlockedStage = 1;
    let currentPreviewIndex = 0;
    let autoScrollTerminal = true;
    let currentPreviewZoom = 0.8; // 80% default zoom out
    let currentDeviceMode = 'desktop';
    let simDeviceMode = 'desktop';

    document.addEventListener('DOMContentLoaded', function () {
        const selectEl = document.getElementById('studioNewsletterSelect');
        if (!currentNewsletterId && selectEl && selectEl.value) {
            currentNewsletterId = selectEl.value;
        }

        if (currentNewsletterId) {
            loadAudienceForNewsletter(currentNewsletterId);
        }
        
        goToStage(1);
        refreshQueueMetrics();
        setInterval(refreshQueueMetrics, 4000);
        setPreviewZoom(0.8);
        setDeviceMode('desktop');

        // Close mailbox modal on single ESC keypress
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeFullMailboxModal();
                closeQuickEditModal();
            }
        }, true);

        // When native browser fullscreen exits via ESC, immediately close modal too so 1 ESC closes both!
        const onFsChange = function() {
            const isFs = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement);
            if (!isFs) {
                const modal = document.getElementById('mailboxSimulatorModal');
                if (modal && !modal.classList.contains('hidden')) {
                    closeFullMailboxModal(true);
                }
            }
        };

        ['fullscreenchange', 'webkitfullscreenchange', 'mozfullscreenchange', 'MSFullscreenChange'].forEach(evt => {
            document.addEventListener(evt, onFsChange);
        });
    });

    function goToStage(stageNumber) {
        if (stageNumber > maxUnlockedStage + 1) return;

        currentStage = stageNumber;
        if (stageNumber > maxUnlockedStage) {
            maxUnlockedStage = stageNumber;
        }

        // Update Stepper Ribbon Visuals
        for (let i = 1; i <= 4; i++) {
            const stepEl = document.getElementById(`stepNav${i}`);
            const badgeEl = document.getElementById(`stepBadge${i}`);
            const panelEl = document.getElementById(`stagePanel${i}`);

            if (!stepEl || !panelEl) continue;

            if (i === currentStage) {
                // Active Stage
                stepEl.className = 'relative px-3 py-2 rounded-xl bg-white dark:bg-[#111114] border-2 border-amber-400 dark:border-amber-400 shadow-sm flex items-center space-x-2 cursor-pointer transition-all ring-2 ring-amber-400/20';
                badgeEl.className = 'w-5 h-5 rounded-lg bg-amber-500 text-zinc-950 font-black flex items-center justify-center text-3xs shrink-0 shadow-2xs';
                badgeEl.innerHTML = i;
                panelEl.classList.remove('hidden');
            } else if (i < currentStage || i <= maxUnlockedStage) {
                // Completed / Accessible Stage
                stepEl.className = 'relative px-3 py-2 rounded-xl bg-white/90 dark:bg-[#111114]/90 border border-emerald-300 dark:border-emerald-800/80 shadow-2xs flex items-center space-x-2 cursor-pointer transition-all hover:bg-emerald-50/40 dark:hover:bg-emerald-950/20';
                badgeEl.className = 'w-5 h-5 rounded-lg bg-emerald-500 text-white font-bold flex items-center justify-center text-3xs shrink-0';
                badgeEl.innerHTML = '<i class="fas fa-check text-4xs"></i>';
                panelEl.classList.add('hidden');
            } else {
                // Locked Stage
                stepEl.className = 'relative px-3 py-2 rounded-xl bg-zinc-50/70 dark:bg-[#0d0d10]/70 border border-zinc-200/70 dark:border-zinc-800/60 opacity-60 flex items-center space-x-2 cursor-not-allowed select-none transition-all';
                badgeEl.className = 'w-5 h-5 rounded-lg bg-zinc-200 dark:bg-zinc-800 text-zinc-400 font-bold flex items-center justify-center text-3xs shrink-0';
                badgeEl.innerHTML = i;
                panelEl.classList.add('hidden');
            }
        }
    }

    function onNewsletterSelected(selectEl) {
        currentNewsletterId = selectEl.value;
        if (currentNewsletterId) {
            loadAudienceForNewsletter(currentNewsletterId);
        }
    }

    function loadAudienceForNewsletter(newsletterId) {
        if (!newsletterId) return;

        const tbody = document.getElementById('audienceTableBody');
        const loadingState = document.getElementById('audienceLoadingState');
        const countDisplay = document.getElementById('selectedLeadCountDisplay');
        const badge = document.getElementById('audienceCountBadge');

        if (tbody) tbody.innerHTML = '';
        if (loadingState) loadingState.classList.remove('hidden');
        if (badge) badge.textContent = 'Resolving...';
        if (countDisplay) countDisplay.textContent = 'Resolving contacts...';

        fetch(`/dispatch/audience?newsletter_id=${encodeURIComponent(newsletterId)}`)
            .then(res => res.json())
            .then(data => {
                if (loadingState) loadingState.classList.add('hidden');
                if (!data.success) {
                    if (tbody) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="5" class="p-6 text-center text-rose-500 text-xs">
                                    <i class="fas fa-triangle-exclamation text-base mb-1"></i>
                                    <p>${escapeHtml(data.message || 'Error loading audience.')}</p>
                                </td>
                            </tr>
                        `;
                    }
                    if (badge) badge.textContent = '0 Selected';
                    if (countDisplay) countDisplay.textContent = '0 leads loaded';
                    return;
                }

                currentAudienceData = data.contacts || [];
                selectedContactIds.clear();

                // Select all contacts by default
                currentAudienceData.forEach(c => selectedContactIds.add(c.id));

                renderAudienceTable();
                updateSelectionCounters();
                populatePreviewSelector();

                // Update Broadcast Info Card in Stage 1 & Stage 4
                if (data.newsletter) {
                    const n = data.newsletter;
                    const stage1Title = document.getElementById('summaryStage1Title');
                    const stage1Campaign = document.getElementById('summaryStage1Campaign');
                    const stage1Subject = document.getElementById('summaryStage1Subject');
                    const stage1Tags = document.getElementById('summaryStage1Tags');
                    const stage1EditBtn = document.getElementById('stage1EditComposerBtn');
                    const stage4Title = document.getElementById('stage4BroadcastTitle');

                    if (stage1Title) stage1Title.textContent = n.title || 'Untitled Broadcast';
                    if (stage4Title) stage4Title.textContent = n.title || 'Untitled Broadcast';
                    if (stage1Campaign) stage1Campaign.textContent = n.campaign_name || 'Unassigned Campaign';
                    if (stage1Subject) stage1Subject.textContent = n.subject_template || '(No subject template configured)';
                    if (stage1EditBtn) stage1EditBtn.href = `/newsletter-form/${n.id}`;

                    if (stage1Tags) {
                        if (n.tags && n.tags.length > 0) {
                            stage1Tags.innerHTML = n.tags.map(t => `<span class="inline-flex items-center px-2 py-0.2 rounded-full text-3xs font-semibold bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/80 dark:border-amber-500/30"><i class="fas fa-tag mr-1 text-4xs"></i>${escapeHtml(t)}</span>`).join(' ');
                        } else {
                            stage1Tags.innerHTML = '<span class="text-zinc-400 italic text-3xs">All active contacts (no tag filters)</span>';
                        }
                    }
                }

                if (currentAudienceData.length > 0) {
                    currentPreviewIndex = 0;
                    renderPersonalizedPreview(currentAudienceData[0].id);
                }
            })
            .catch(err => {
                if (loadingState) loadingState.classList.add('hidden');
                if (tbody) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="p-6 text-center text-rose-500 text-xs">
                                <i class="fas fa-circle-exclamation text-base mb-1"></i>
                                <p>Failed to connect: ${escapeHtml(err.message)}</p>
                            </td>
                        </tr>
                    `;
                }
                if (badge) badge.textContent = '0 Selected';
                if (countDisplay) countDisplay.textContent = 'Network error';
            });
    }

    function renderAudienceTable() {
        const tbody = document.getElementById('audienceTableBody');
        const searchInput = document.getElementById('audienceSearchInput');
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        if (!tbody) return;

        tbody.innerHTML = '';

        const filtered = currentAudienceData.filter(c => {
            if (!query) return true;
            return c.email.toLowerCase().includes(query) ||
                   c.name.toLowerCase().includes(query) ||
                   c.company.toLowerCase().includes(query);
        });

        if (filtered.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="p-8 text-center text-zinc-400 text-xs">
                        <p class="font-bold text-zinc-700 dark:text-zinc-300">No matching contacts found</p>
                        <p class="text-3xs text-zinc-400 mt-0.5">Try searching with another keyword.</p>
                    </td>
                </tr>
            `;
            return;
        }

        filtered.forEach(c => {
            const isChecked = selectedContactIds.has(c.id);
            const tr = document.createElement('tr');
            tr.className = `border-b border-zinc-100 dark:border-zinc-800/80 transition-colors ${isChecked ? 'hover:bg-amber-50/30 dark:hover:bg-amber-950/15' : 'opacity-40 bg-zinc-50/50 dark:bg-zinc-900/30'}`;
            
            const initials = (c.firstname ? c.firstname.charAt(0) : '') + (c.lastname ? c.lastname.charAt(0) : '') || c.email.charAt(0).toUpperCase();

            const tagsHtml = c.tags.map(t => `<span class="inline-flex items-center px-1.5 py-0.2 rounded-full text-3xs font-semibold bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/60 dark:border-amber-500/30">${escapeHtml(t)}</span>`).join(' ');

            tr.innerHTML = `
                <td class="py-2.5 pl-4 pr-2 w-8">
                    <input type="checkbox" value="${c.id}" ${isChecked ? 'checked' : ''} onchange="toggleContactSelection(${c.id}, this.checked)" class="rounded border-zinc-300 dark:border-zinc-700 text-amber-500 focus:ring-amber-400 w-3.5 h-3.5 cursor-pointer">
                </td>
                <td class="py-2.5 px-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-6 h-6 rounded-md bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-300 font-bold text-4xs flex items-center justify-center border border-amber-200/60 dark:border-amber-500/30 shrink-0">
                            ${escapeHtml(initials)}
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-xs text-zinc-900 dark:text-white truncate">
                                ${escapeHtml(c.name || 'Unnamed Lead')}
                            </div>
                            <div class="text-3xs text-zinc-400 font-mono truncate">${escapeHtml(c.email)}</div>
                        </div>
                    </div>
                </td>
                <td class="py-2.5 px-3 text-xs text-zinc-600 dark:text-zinc-300 font-medium">
                    <div class="flex items-center gap-1">
                        <i class="fas fa-building text-zinc-400 text-4xs"></i>
                        <span class="truncate">${escapeHtml(c.company || '—')}</span>
                    </div>
                </td>
                <td class="py-2.5 px-3">
                    <div class="flex flex-wrap gap-1">${tagsHtml || '<span class="text-3xs text-zinc-400 italic">No tags</span>'}</div>
                </td>
                <td class="py-2.5 pl-2 pr-4 text-right whitespace-nowrap">
                    <div class="flex items-center justify-end space-x-1">
                        <button type="button" onclick="openQuickEditModal(${c.id})" title="Quick Edit Lead Details" class="p-1 text-zinc-400 hover:text-zinc-900 dark:hover:text-amber-200 rounded hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors text-xs cursor-pointer">
                            <i class="fas fa-pen text-3xs"></i>
                        </button>
                        <button type="button" onclick="renderPersonalizedPreview(${c.id}); goToStage(3);" title="Inspect Live Email Preview" class="px-2 py-0.5 text-3xs font-semibold text-amber-800 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100 dark:hover:bg-amber-900/50 border border-amber-200/80 dark:border-amber-500/30 rounded transition-colors flex items-center gap-1 cursor-pointer">
                            <i class="fas fa-eye text-3xs text-amber-500"></i>
                            <span>Preview</span>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function toggleContactSelection(id, checked) {
        if (checked) {
            selectedContactIds.add(id);
        } else {
            selectedContactIds.delete(id);
        }
        updateSelectionCounters();
        renderAudienceTable();
    }

    function selectAllContacts(selectAll) {
        if (selectAll) {
            currentAudienceData.forEach(c => selectedContactIds.add(c.id));
        } else {
            selectedContactIds.clear();
        }
        updateSelectionCounters();
        renderAudienceTable();
    }

    function updateSelectionCounters() {
        const countDisplay = document.getElementById('selectedLeadCountDisplay');
        const badge = document.getElementById('audienceCountBadge');
        const stage4SelectedCount = document.getElementById('stage4TargetCount');
        const stage4TotalCount = document.getElementById('stage4TotalCount');
        const total = currentAudienceData.length;
        const selected = selectedContactIds.size;

        if (countDisplay) {
            countDisplay.innerHTML = `<span class="text-amber-600 dark:text-amber-400 font-bold">${selected}</span> of ${total} leads selected`;
        }
        if (badge) {
            badge.textContent = `${selected} / ${total} Targeted`;
        }
        if (stage4SelectedCount) {
            stage4SelectedCount.textContent = `${selected} Leads Selected`;
        }
        if (stage4TotalCount) {
            stage4TotalCount.textContent = selected;
        }
    }

    function populatePreviewSelector() {
        const selector = document.getElementById('previewContactSelector');
        const simSelector = document.getElementById('simMailboxLeadSelector');

        [selector, simSelector].forEach(sel => {
            if (!sel) return;
            sel.innerHTML = '';
            currentAudienceData.forEach((c, index) => {
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = `${index + 1}. ${c.name || 'Lead'} (${c.company}) — ${c.email}`;
                sel.appendChild(opt);
            });
        });
    }

    function navigatePreview(direction) {
        if (currentAudienceData.length === 0) return;
        currentPreviewIndex += direction;
        if (currentPreviewIndex < 0) currentPreviewIndex = currentAudienceData.length - 1;
        if (currentPreviewIndex >= currentAudienceData.length) currentPreviewIndex = 0;

        const contact = currentAudienceData[currentPreviewIndex];
        if (contact) {
            renderPersonalizedPreview(contact.id);
        }
    }

    function setPreviewZoom(scale) {
        currentPreviewZoom = scale;
        const container = document.getElementById('previewRenderedBody');
        const zoomText = document.getElementById('previewZoomLevelText');
        if (container) {
            container.style.transform = `scale(${scale})`;
            container.style.transformOrigin = 'top center';
            container.style.width = (100 / scale) + '%';
        }
        if (zoomText) zoomText.textContent = Math.round(scale * 100) + '%';

        document.querySelectorAll('.zoom-preset-btn').forEach(btn => {
            const val = parseFloat(btn.getAttribute('data-zoom'));
            if (Math.abs(val - scale) < 0.04) {
                btn.className = 'zoom-preset-btn px-2 py-0.5 rounded text-3xs font-bold bg-amber-500 text-zinc-950 shadow-2xs transition-all';
            } else {
                btn.className = 'zoom-preset-btn px-2 py-0.5 rounded text-3xs font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-all';
            }
        });
    }

    function zoomIn() {
        setPreviewZoom(Math.min(1.4, Math.round((currentPreviewZoom + 0.1) * 10) / 10));
    }

    function zoomOut() {
        setPreviewZoom(Math.max(0.5, Math.round((currentPreviewZoom - 0.1) * 10) / 10));
    }

    function setDeviceMode(mode) {
        currentDeviceMode = mode;
        const desktopContainer = document.getElementById('previewDesktopContainer');
        const mobileContainer = document.getElementById('previewMobileContainer');
        const mainWrapper = document.getElementById('previewViewWrapper');

        ['desktop', 'mobile', 'split'].forEach(m => {
            const btn = document.getElementById(`btnDeviceMode_${m}`);
            if (btn) {
                if (m === mode) {
                    btn.className = 'device-toggle-btn px-2.5 py-1 rounded text-3xs font-bold bg-amber-500 text-zinc-950 shadow-2xs transition-all flex items-center gap-1 cursor-pointer';
                } else {
                    btn.className = 'device-toggle-btn px-2.5 py-1 rounded text-3xs font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-all flex items-center gap-1 cursor-pointer';
                }
            }
        });

        if (mode === 'desktop') {
            if (mainWrapper) mainWrapper.className = 'block';
            if (desktopContainer) {
                desktopContainer.classList.remove('hidden');
                desktopContainer.className = 'rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-[#0d0d10] shadow-sm';
            }
            if (mobileContainer) mobileContainer.classList.add('hidden');
        } else if (mode === 'mobile') {
            if (mainWrapper) mainWrapper.className = 'flex justify-center py-1';
            if (desktopContainer) desktopContainer.classList.add('hidden');
            if (mobileContainer) {
                mobileContainer.classList.remove('hidden');
                mobileContainer.className = 'w-full flex justify-center';
            }
        } else if (mode === 'split') {
            if (mainWrapper) mainWrapper.className = 'grid grid-cols-1 lg:grid-cols-12 gap-3 items-start';
            if (desktopContainer) {
                desktopContainer.classList.remove('hidden');
                desktopContainer.className = 'lg:col-span-8 rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-[#0d0d10] shadow-sm';
            }
            if (mobileContainer) {
                mobileContainer.classList.remove('hidden');
                mobileContainer.className = 'lg:col-span-4 flex justify-center';
            }
        }
    }

    function renderPersonalizedPreview(contactId) {
        const contact = currentAudienceData.find(c => c.id == contactId);
        if (!contact) return;

        currentPreviewIndex = currentAudienceData.findIndex(c => c.id == contactId);

        const subjectEl = document.getElementById('previewRenderedSubject');
        const bodyEl = document.getElementById('previewRenderedBody');
        const rawBodyEl = document.getElementById('previewRawBody');
        const metaEl = document.getElementById('previewRecipientMeta');
        const tokensPod = document.getElementById('previewTokensDetected');
        const counterEl = document.getElementById('previewLeadCounter');

        // Mobile preview elements
        const mobileSubjectEl = document.getElementById('previewMobileSubject');
        const mobileRecipientEl = document.getElementById('previewMobileRecipient');
        const mobileBodyEl = document.getElementById('previewMobileRenderedBody');

        const renderedSubj = contact.preview_subject || '(No Subject Line)';
        const renderedBody = contact.preview_body || '<p class="text-zinc-400 italic">No body content</p>';

        if (subjectEl) subjectEl.textContent = renderedSubj;
        if (bodyEl) bodyEl.innerHTML = renderedBody;
        if (rawBodyEl) rawBodyEl.textContent = contact.preview_body || '(Empty body)';

        if (mobileSubjectEl) mobileSubjectEl.textContent = renderedSubj;
        if (mobileRecipientEl) mobileRecipientEl.innerHTML = `<span class="font-bold text-zinc-800 dark:text-zinc-200">${escapeHtml(contact.name || 'Lead')}</span> &bull; <span class="font-mono text-zinc-500 text-4xs">${escapeHtml(contact.email)}</span>`;
        if (mobileBodyEl) mobileBodyEl.innerHTML = renderedBody;

        setPreviewZoom(currentPreviewZoom);

        if (metaEl) {
            metaEl.innerHTML = `
                <div class="flex items-center space-x-2 truncate">
                    <span class="font-bold text-zinc-900 dark:text-white text-xs">${escapeHtml(contact.name || 'Lead')}</span>
                    <span class="text-zinc-300 dark:text-zinc-700">•</span>
                    <span class="text-zinc-600 dark:text-zinc-400 font-medium text-xs">${escapeHtml(contact.company || 'No Company')}</span>
                    <span class="text-zinc-300 dark:text-zinc-700">•</span>
                    <span class="font-mono text-amber-600 dark:text-amber-400 text-xs">${escapeHtml(contact.email)}</span>
                </div>
            `;
        }

        if (counterEl) {
            counterEl.textContent = `Lead ${currentPreviewIndex + 1} of ${currentAudienceData.length}`;
        }

        if (tokensPod) {
            tokensPod.innerHTML = `
                <span class="inline-flex items-center px-1.5 py-0.2 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-mono text-4xs border border-zinc-200 dark:border-zinc-700">[[firstname]]: <strong>${escapeHtml(contact.firstname || '—')}</strong></span>
                <span class="inline-flex items-center px-1.5 py-0.2 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-mono text-4xs border border-zinc-200 dark:border-zinc-700">[[lastname]]: <strong>${escapeHtml(contact.lastname || '—')}</strong></span>
                <span class="inline-flex items-center px-1.5 py-0.2 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-mono text-4xs border border-zinc-200 dark:border-zinc-700">[[company]]: <strong>${escapeHtml(contact.company || '—')}</strong></span>
                <span class="inline-flex items-center px-1.5 py-0.2 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-mono text-4xs border border-zinc-200 dark:border-zinc-700">[[email]]: <strong>${escapeHtml(contact.email || '—')}</strong></span>
            `;
        }

        const selector = document.getElementById('previewContactSelector');
        if (selector && selector.value != contactId) {
            selector.value = contactId;
        }

        // Also update Mailbox Popup if opened
        const modal = document.getElementById('mailboxSimulatorModal');
        if (modal && !modal.classList.contains('hidden')) {
            updateMailboxSimulatorContent();
        }
    }

    // ========================================================
    // AUTHENTIC MAILBOX EXPERIENCE POPUP SIMULATOR
    // ========================================================
    function openFullMailboxModal() {
        const modal = document.getElementById('mailboxSimulatorModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        updateMailboxSimulatorContent();

        // Trigger native true monitor fullscreen (the cool immersive mode)
        try {
            const req = modal.requestFullscreen || modal.webkitRequestFullscreen || modal.mozRequestFullScreen || modal.msRequestFullscreen;
            if (req && !document.fullscreenElement) {
                req.call(modal).catch(() => {});
            }
        } catch (e) {}
    }

    function closeFullMailboxModal(fromFs = false) {
        const modal = document.getElementById('mailboxSimulatorModal');
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');

        if (!fromFs) {
            try {
                const isFs = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement);
                if (isFs) {
                    const exit = document.exitFullscreen || document.webkitExitFullscreen || document.mozCancelFullScreen || document.msExitFullscreen;
                    if (exit) exit.call(document).catch(() => {});
                }
            } catch (e) {}
        }
    }

    function toggleBrowserFullscreen() {
        const modal = document.getElementById('mailboxSimulatorModal');
        try {
            if (!document.fullscreenElement) {
                if (modal && modal.requestFullscreen) {
                    modal.requestFullscreen().catch(() => {});
                } else if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen().catch(() => {});
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen().catch(() => {});
                }
            }
        } catch (e) {}
    }

    function setSimDeviceMode(mode) {
        simDeviceMode = mode;
        const desktopFrame = document.getElementById('simDesktopFrame');
        const mobileFrame = document.getElementById('simMobileFrame');
        const btnDesk = document.getElementById('simBtnDesktop');
        const btnMob = document.getElementById('simBtnMobile');

        if (mode === 'desktop') {
            if (desktopFrame) desktopFrame.classList.remove('hidden');
            if (mobileFrame) mobileFrame.classList.add('hidden');
            if (btnDesk) {
                btnDesk.className = 'px-3 py-1 rounded-full text-3xs font-bold bg-[#c2e7ff] dark:bg-[#004a77] text-[#001d35] dark:text-[#c2e7ff] shadow-2xs transition-all flex items-center gap-1.5 cursor-pointer';
            }
            if (btnMob) {
                btnMob.className = 'px-3 py-1 rounded-full text-3xs font-semibold text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-all flex items-center gap-1.5 cursor-pointer';
            }
        } else {
            if (desktopFrame) desktopFrame.classList.add('hidden');
            if (mobileFrame) mobileFrame.classList.remove('hidden');
            if (btnDesk) {
                btnDesk.className = 'px-3 py-1 rounded-full text-3xs font-semibold text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-all flex items-center gap-1.5 cursor-pointer';
            }
            if (btnMob) {
                btnMob.className = 'px-3 py-1 rounded-full text-3xs font-bold bg-[#c2e7ff] dark:bg-[#004a77] text-[#001d35] dark:text-[#c2e7ff] shadow-2xs transition-all flex items-center gap-1.5 cursor-pointer';
            }
        }
    }

    function toggleSimStar(btn) {
        if (!btn) return;
        const icon = btn.querySelector('i');
        if (icon) {
            if (icon.classList.contains('far')) {
                icon.classList.remove('far', 'text-zinc-400');
                icon.classList.add('fas', 'text-amber-400');
            } else {
                icon.classList.remove('fas', 'text-amber-400');
                icon.classList.add('far', 'text-zinc-400');
            }
        }
    }

    function toggleGmailDetails() {
        const detailsEl = document.getElementById('simGmailDetailsCard');
        if (detailsEl) {
            detailsEl.classList.toggle('hidden');
        }
    }

    function updateMailboxSimulatorContent() {
        if (currentAudienceData.length === 0) return;
        const contact = currentAudienceData[currentPreviewIndex];
        if (!contact) return;

        const subjectEl = document.getElementById('simMailboxSubject');
        const recipientEl = document.getElementById('simMailboxRecipient');
        const recipientEmailEl = document.getElementById('simMailboxRecipientEmail');
        const dateEl = document.getElementById('simMailboxDate');
        const iframe = document.getElementById('simMailboxIframe');
        const mobileIframe = document.getElementById('simMailboxMobileIframe');
        const selector = document.getElementById('simMailboxLeadSelector');
        const counter = document.getElementById('simMailboxCounter');

        // Detailed dropdown fields
        const detailTo = document.getElementById('simDetailTo');
        const detailDate = document.getElementById('simDetailDate');
        const detailSubject = document.getElementById('simDetailSubject');

        const renderedSubj = contact.preview_subject || '(No Subject Line)';
        const renderedBody = contact.preview_body || '<p style="color: #888; font-style: italic;">No body content provided.</p>';

        if (subjectEl) subjectEl.textContent = renderedSubj;
        if (recipientEl) recipientEl.textContent = contact.name || 'Recipient Lead';
        if (recipientEmailEl) recipientEmailEl.textContent = `<${contact.email}>`;
        if (counter) counter.textContent = `${currentPreviewIndex + 1} of ${currentAudienceData.length}`;

        const now = new Date();
        const formattedDate = now.toLocaleDateString([], { month: 'short', day: 'numeric', year: 'numeric' }) + ', ' + now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        if (dateEl) {
            dateEl.textContent = formattedDate;
        }
        if (detailDate) {
            detailDate.textContent = formattedDate;
        }
        if (detailTo) {
            detailTo.textContent = `${contact.name || 'Lead'} <${contact.email}>`;
        }
        if (detailSubject) {
            detailSubject.textContent = renderedSubj;
        }

        if (selector && selector.value != contact.id) {
            selector.value = contact.id;
        }

        const completeEmailHtml = `
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <style>
                    body {
                        margin: 0;
                        padding: 24px;
                        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                        background-color: #ffffff;
                        color: #1a1a1a;
                        line-height: 1.5;
                        font-size: 14px;
                    }
                    img { max-width: 100%; height: auto; }
                    a { color: #d97706; }
                </style>
            </head>
            <body>
                ${renderedBody}
            </body>
            </html>
        `;

        if (iframe) {
            iframe.srcdoc = completeEmailHtml;
        }
        if (mobileIframe) {
            mobileIframe.srcdoc = completeEmailHtml;
        }
    }

    function switchPreviewTab(tab) {
        const renderedPane = document.getElementById('previewRenderedBodyPane');
        const rawPane = document.getElementById('previewRawBodyPane');
        const btnRendered = document.getElementById('tabBtnRendered');
        const btnRaw = document.getElementById('tabBtnRaw');
        const zoomControls = document.getElementById('previewZoomControlsBar');

        if (tab === 'rendered') {
            if (renderedPane) renderedPane.classList.remove('hidden');
            if (rawPane) rawPane.classList.add('hidden');
            if (zoomControls) zoomControls.classList.remove('hidden');
            if (btnRendered) {
                btnRendered.className = 'px-2.5 py-0.5 rounded text-3xs font-bold bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white shadow-2xs transition-all';
            }
            if (btnRaw) {
                btnRaw.className = 'px-2.5 py-0.5 rounded text-3xs font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-all';
            }
        } else {
            if (renderedPane) renderedPane.classList.add('hidden');
            if (rawPane) rawPane.classList.remove('hidden');
            if (zoomControls) zoomControls.classList.add('hidden');
            if (btnRendered) {
                btnRendered.className = 'px-2.5 py-0.5 rounded text-3xs font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-all';
            }
            if (btnRaw) {
                btnRaw.className = 'px-2.5 py-0.5 rounded text-3xs font-bold bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white shadow-2xs transition-all';
            }
        }
    }

    function openQuickEditModal(contactId) {
        const contact = currentAudienceData.find(c => c.id == contactId);
        if (!contact) return;

        document.getElementById('editContactId').value = contact.id;
        document.getElementById('editFirstname').value = contact.firstname || '';
        document.getElementById('editLastname').value = contact.lastname || '';
        document.getElementById('editEmail').value = contact.email || '';
        document.getElementById('editCompany').value = contact.company || '';
        document.getElementById('quickEditModal').classList.remove('hidden');
    }

    function closeQuickEditModal() {
        document.getElementById('quickEditModal').classList.add('hidden');
    }

    function saveQuickEdit(e) {
        e.preventDefault();
        const cid = document.getElementById('editContactId').value;
        const fname = document.getElementById('editFirstname').value;
        const lname = document.getElementById('editLastname').value;
        const email = document.getElementById('editEmail').value;
        const comp = document.getElementById('editCompany').value;

        fetch('/dispatch/update-contact', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                contact_id: cid,
                firstname: fname,
                lastname: lname,
                email: email,
                company: comp
            })
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                throw new Error(data.message || 'HTTP ' + res.status + ' error occurred.');
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                closeQuickEditModal();
                loadAudienceForNewsletter(currentNewsletterId);
                appendStudioLog(`[UPDATED] Lead details saved for ${email}`, 'success');
            } else {
                alert(data.message || 'Update failed.');
            }
        })
        .catch(err => alert('Lead Update Notice: ' + err.message));
    }

    // ========================================================
    // ADVANCED CLI DAEMON STREAMING ENGINE & VIEWPORT TRACKER
    // ========================================================
    let streamQueue = [];
    let isStreamingLogs = false;
    let streamDoneCallback = null;
    let liveTxCounter = 0;
    let liveRescuedCounter = 0;
    let liveErrorsCounter = 0;
    let batchStartTime = null;

    function setEngineStatus(mode, detail = '') {
        const liveBadge = document.getElementById('termLiveBadge');
        const hudStatus = document.getElementById('termHudStatus');
        const statusPrompt = document.getElementById('termStatusText');

        if (mode === 'live') {
            if (liveBadge) {
                liveBadge.className = 'text-emerald-400 font-bold flex items-center gap-1 text-4xs pl-1.5 border-l border-zinc-800';
                liveBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span> LIVE STREAMING';
            }
            if (hudStatus) {
                hudStatus.className = 'text-emerald-400 font-semibold flex items-center gap-1';
                hudStatus.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> SOCKET STREAM ACTIVE';
            }
            if (statusPrompt && detail) statusPrompt.textContent = detail;
        } else if (mode === 'staging') {
            if (liveBadge) {
                liveBadge.className = 'text-amber-400 font-bold flex items-center gap-1 text-4xs pl-1.5 border-l border-zinc-800';
                liveBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> STAGING';
            }
            if (hudStatus) {
                hudStatus.className = 'text-amber-400 font-semibold flex items-center gap-1';
                hudStatus.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> LEXING AST / PARSING TOKENS';
            }
            if (statusPrompt && detail) statusPrompt.textContent = detail;
        } else {
            if (liveBadge) {
                liveBadge.className = 'text-zinc-400 font-bold flex items-center gap-1 text-4xs pl-1.5 border-l border-zinc-800';
                liveBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> STANDBY';
            }
            if (hudStatus) {
                hudStatus.className = 'text-emerald-400 font-semibold flex items-center gap-1';
                hudStatus.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> READY / LISTENING';
            }
            if (statusPrompt) statusPrompt.textContent = 'worker idle & awaiting batch command...';
        }
    }

    function enqueueTerminalLogs(logs, onDone) {
        if (!logs) {
            if (onDone) onDone();
            return;
        }
        const logList = Array.isArray(logs) ? logs : [logs];
        logList.forEach(item => streamQueue.push(item));

        if (onDone) {
            const prevDone = streamDoneCallback;
            streamDoneCallback = () => {
                if (prevDone) prevDone();
                onDone();
            };
        }

        if (!isStreamingLogs) {
            processNextStreamLog();
        }
    }

    function processNextStreamLog() {
        if (streamQueue.length === 0) {
            isStreamingLogs = false;
            if (streamDoneCallback) {
                const cb = streamDoneCallback;
                streamDoneCallback = null;
                cb();
            }
            return;
        }

        isStreamingLogs = true;
        const rawMsg = streamQueue.shift();
        renderTerminalLogLine(rawMsg);

        // Variable microsecond delay creates authentic intensive backend stream feeling
        let delay = 35;
        if (rawMsg.includes('[DISPATCH:')) delay = 55;
        else if (rawMsg.includes('[DNS:RESOLVE]')) delay = 40;
        else if (rawMsg.includes('[SOCKET:TCP]')) delay = 45;
        else if (rawMsg.includes('[TLS:1.3]')) delay = 50;
        else if (rawMsg.includes('[SMTP:AUTH]')) delay = 40;
        else if (rawMsg.includes('[MIME:STREAM]')) delay = 60;
        else if (rawMsg.includes('[250:ACK]')) delay = 70;
        else if (rawMsg.includes('[DELIVERED]')) delay = 60;
        else if (rawMsg.includes('[FAILOVER:ENGAGED]')) delay = 90;
        else if (rawMsg.includes('[BATCH:')) delay = 100;
        else if (rawMsg.includes('[TOKEN:EVAL]')) delay = 25;

        setTimeout(processNextStreamLog, delay);
    }

    async function readNdjsonStream(url, options, onEvent) {
        const response = await fetch(url, options);
        if (!response.ok) {
            let errText = response.statusText;
            try {
                const errData = await response.json();
                if (errData && errData.message) errText = errData.message;
            } catch (e) {}
            throw new Error(errText || `HTTP ${response.status}`);
        }

        const reader = response.body.getReader();
        const decoder = new TextDecoder('utf-8');
        let buffer = '';

        while (true) {
            const { done, value } = await reader.read();
            if (done) {
                if (buffer && buffer.trim()) {
                    const lines = buffer.split('\n');
                    for (const line of lines) {
                        if (line.trim()) {
                            try {
                                const evt = JSON.parse(line.trim());
                                onEvent(evt);
                            } catch (e) {}
                        }
                    }
                }
                break;
            }

            buffer += decoder.decode(value, { stream: true });
            const lines = buffer.split('\n');
            buffer = lines.pop(); // keep partial chunk

            for (const line of lines) {
                if (!line.trim()) continue;
                try {
                    const evt = JSON.parse(line.trim());
                    onEvent(evt);
                } catch (e) {
                    console.error('Failed to parse NDJSON line:', line, e);
                }
            }
        }
    }

    async function triggerCustomQueue() {
        if (selectedContactIds.size === 0) {
            alert('Please select at least 1 lead to queue.');
            return;
        }

        const btn = document.getElementById('btnStageQueue');
        const icon = document.getElementById('iconStageQueue');
        if (btn) btn.disabled = true;
        if (icon) icon.className = 'fas fa-spinner fa-spin text-xs';

        // Bring terminal smoothly into active viewport
        const termCard = document.getElementById('studioTerminalCard');
        if (termCard) {
            termCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        setEngineStatus('staging', `initiating queue pipeline for ${selectedContactIds.size} lead(s)...`);
        renderTerminalLogLine(`[SYSTEM] Staging queue buffer for ${selectedContactIds.size} recipient(s)...`);

        try {
            await readNdjsonStream('/dispatch/queue-custom-stream', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/x-ndjson',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    newsletter_id: currentNewsletterId,
                    contact_ids: Array.from(selectedContactIds)
                })
            }, (evt) => {
                if (evt.type === 'log') {
                    renderTerminalLogLine(evt.message);
                }
            });

            refreshQueueMetrics();
            setEngineStatus('idle');
            const btnFlush = document.getElementById('btnStudioFlush');
            if (btnFlush) btnFlush.disabled = false;
        } catch (err) {
            renderTerminalLogLine('[ERROR] Staging failed: ' + err.message);
            setEngineStatus('idle');
        } finally {
            if (btn) btn.disabled = false;
            if (icon) icon.className = 'fas fa-layer-group text-xs text-amber-400 dark:text-amber-500';
        }
    }

    function triggerStudioFlush() {
        if (isFlushingActive) return;
        isFlushingActive = true;
        batchStartTime = Date.now();

        const btn = document.getElementById('btnStudioFlush');
        const icon = document.getElementById('iconStudioFlush');
        const progressContainer = document.getElementById('dispatchProgressContainer');
        const progressBar = document.getElementById('dispatchProgressBar');
        const progressText = document.getElementById('dispatchProgressText');
        const statusBadge = document.getElementById('dispatchStatusBadge');
        const statusDot = document.getElementById('dispatchStatusDot');
        const statusTitle = document.getElementById('dispatchStatusTitle');
        const failoverBadge = document.getElementById('dispatchFailoverBadge');
        const failoverCountEl = document.getElementById('dispatchFailoverCount');
        const countMetrics = document.getElementById('dispatchCountMetrics');
        const percentMetrics = document.getElementById('dispatchPercentMetrics');
        const latencyMetric = document.getElementById('dispatchLatencyMetric');

        if (btn) btn.disabled = true;
        if (icon) icon.className = 'fas fa-spinner fa-spin text-xs';
        if (progressContainer) progressContainer.classList.remove('hidden');

        // Bring terminal smoothly into active viewport
        const termCard = document.getElementById('studioTerminalCard');
        if (termCard) {
            termCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        setEngineStatus('live', 'spawning asynchronous socket stream...');
        renderTerminalLogLine('[CORE] Outbound socket transmission engine initialized.');

        const initialStaged = parseInt(document.getElementById('studioInQueue')?.textContent || '0', 10) || 1;
        let totalTarget = Math.max(1, initialStaged);

        if (statusBadge) {
            statusBadge.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-sky-950/80 text-sky-300 border border-sky-700/70 font-mono text-3xs font-bold uppercase tracking-wider';
        }
        if (statusDot) {
            statusDot.className = 'w-1.5 h-1.5 rounded-full bg-sky-400 animate-pulse';
        }
        if (statusTitle) {
            statusTitle.textContent = 'TRANSMITTING SOCKET STREAM';
        }
        if (failoverBadge) {
            failoverBadge.classList.add('hidden');
        }
        if (countMetrics) {
            countMetrics.textContent = `0 / ${totalTarget} DELIVERED`;
        }
        if (percentMetrics) {
            percentMetrics.textContent = '10%';
            percentMetrics.className = 'text-sky-400 font-bold font-mono text-xs';
        }
        if (progressBar) {
            progressBar.className = 'h-full rounded-full transition-all duration-300 ease-out bg-gradient-to-r from-sky-500 via-teal-400 to-emerald-400 shadow-[0_0_12px_rgba(14,165,233,0.4)]';
            progressBar.style.width = '10%';
        }
        if (progressText) {
            progressText.className = 'text-zinc-300 font-medium truncate';
            progressText.textContent = 'Acquiring queue lock • Establishing TCP/TLS sockets...';
        }
        if (latencyMetric) {
            latencyMetric.textContent = 'SOCKET: CONNECTING';
        }

        async function processBatch() {
            try {
                let lastDoneEvent = null;

                await readNdjsonStream('/queue/flush-stream', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/x-ndjson',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ limit: 15 })
                }, (evt) => {
                    if (evt.type === 'log') {
                        renderTerminalLogLine(evt.message);
                    } else if (evt.type === 'delivered') {
                        renderTerminalLogLine(evt.message);
                        const delivered = liveTxCounter;
                        const pct = Math.min(95, Math.max(20, Math.round((delivered / totalTarget) * 100)));
                        if (progressBar) progressBar.style.width = `${pct}%`;
                        if (percentMetrics) percentMetrics.textContent = `${pct}%`;
                        if (countMetrics) countMetrics.textContent = `${delivered} / ${totalTarget} DELIVERED`;
                        if (progressText) progressText.textContent = `Delivered <${evt.email}> • Remote peer 250 ACK recorded`;
                        if (latencyMetric) latencyMetric.textContent = 'ACK: 250 2.0.0';
                        if (evt.rescued && failoverBadge && failoverCountEl) {
                            failoverBadge.classList.remove('hidden');
                            failoverCountEl.textContent = `${liveRescuedCounter} FAILOVER RESCUE`;
                        }
                    } else if (evt.type === 'recipient_failed') {
                        renderTerminalLogLine(evt.message);
                        if (progressText) progressText.textContent = `Relay pipeline exhausted for <${evt.email}>`;
                    } else if (evt.type === 'done') {
                        lastDoneEvent = evt;
                        if (typeof evt.remaining !== 'undefined') {
                            const remEl = document.getElementById('telemetryRemaining');
                            if (remEl) remEl.textContent = evt.remaining;
                            const hudBuf = document.getElementById('termHudBuffer');
                            if (hudBuf) hudBuf.textContent = `${evt.remaining} Staged`;
                        }
                    }
                });

                refreshQueueMetrics();

                if (lastDoneEvent) {
                    const remaining = lastDoneEvent.remaining || 0;
                    const sentCount = lastDoneEvent.sent_count || 0;
                    const failedCount = lastDoneEvent.failed_count || 0;
                    const failoverCount = lastDoneEvent.failover_count || 0;

                    if (remaining > 0 && sentCount > 0) {
                        if (progressText) progressText.textContent = `Batch acknowledged (${sentCount} sent). ${remaining} remaining in queue...`;
                        renderTerminalLogLine(`[PIPELINE] ${remaining} email(s) remaining in queue. Transmitting next socket batch...`);
                        setTimeout(processBatch, 400);
                        return;
                    }

                    // Cycle finished
                    isFlushingActive = false;
                    if (btn) btn.disabled = false;
                    if (icon) icon.className = 'fas fa-paper-plane text-xs';

                    const totalSent = liveTxCounter || sentCount;
                    const totalErrors = failedCount;

                    if (totalSent === 0 && totalErrors > 0) {
                        if (statusBadge) {
                            statusBadge.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-rose-950/80 text-rose-300 border border-rose-700/70 font-mono text-3xs font-bold uppercase tracking-wider';
                        }
                        if (statusDot) {
                            statusDot.className = 'w-1.5 h-1.5 rounded-full bg-rose-400';
                        }
                        if (statusTitle) {
                            statusTitle.textContent = 'TRANSMISSION HALTED';
                        }
                        if (progressBar) {
                            progressBar.className = 'h-full rounded-full transition-all duration-500 ease-out bg-rose-500 shadow-[0_0_14px_rgba(244,63,94,0.5)]';
                            progressBar.style.width = '100%';
                        }
                        if (percentMetrics) {
                            percentMetrics.textContent = 'FAILED';
                            percentMetrics.className = 'text-rose-400 font-bold font-mono text-xs';
                        }
                        if (countMetrics) {
                            countMetrics.textContent = `0 / ${totalErrors} DELIVERED`;
                        }
                        if (progressText) {
                            progressText.className = 'text-rose-300 font-medium truncate';
                            progressText.textContent = `Transmission halted: 0 sent, ${totalErrors} gateway error(s). Review diagnostics above.`;
                        }
                        if (latencyMetric) {
                            latencyMetric.textContent = 'ERROR 500';
                        }
                        renderTerminalLogLine('[DISPATCH:ABORTED] Transmission cycle halted. Zero emails sent due to gateway errors. Review diagnostics above.');
                        setEngineStatus('idle');
                    } else if (totalErrors > 0 && totalSent > 0) {
                        if (statusBadge) {
                            statusBadge.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-amber-950/80 text-amber-300 border border-amber-700/70 font-mono text-3xs font-bold uppercase tracking-wider';
                        }
                        if (statusDot) {
                            statusDot.className = 'w-1.5 h-1.5 rounded-full bg-amber-400';
                        }
                        if (statusTitle) {
                            statusTitle.textContent = 'CYCLE COMPLETED WITH WARNINGS';
                        }
                        if (progressBar) {
                            progressBar.className = 'h-full rounded-full transition-all duration-500 ease-out bg-gradient-to-r from-amber-500 to-rose-500 shadow-[0_0_12px_rgba(245,158,11,0.4)]';
                            progressBar.style.width = '100%';
                        }
                        if (percentMetrics) {
                            percentMetrics.textContent = `${Math.round((totalSent / (totalSent + totalErrors)) * 100)}%`;
                            percentMetrics.className = 'text-amber-400 font-bold font-mono text-xs';
                        }
                        if (countMetrics) {
                            countMetrics.textContent = `${totalSent} / ${totalSent + totalErrors} DELIVERED`;
                        }
                        if (progressText) {
                            progressText.className = 'text-amber-300 font-medium truncate';
                            progressText.textContent = `Batch finished with warnings: ${totalSent} delivered, ${totalErrors} unrecovered error(s).`;
                        }
                        if (latencyMetric) {
                            latencyMetric.textContent = 'PARTIAL ACK';
                        }
                        renderTerminalLogLine(`[DISPATCH:WARNING] Transmission cycle finished with partial failures (${totalSent} delivered, ${totalErrors} failed).`);
                        setEngineStatus('idle');
                    } else {
                        // 100% Success
                        if (statusBadge) {
                            statusBadge.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-emerald-950/80 text-emerald-300 border border-emerald-600 font-mono text-3xs font-bold uppercase tracking-wider';
                        }
                        if (statusDot) {
                            statusDot.className = 'w-1.5 h-1.5 rounded-full bg-emerald-400';
                        }
                        if (statusTitle) {
                            statusTitle.textContent = 'TRANSMISSION CYCLE CONFIRMED';
                        }
                        if (progressBar) {
                            progressBar.className = 'h-full rounded-full transition-all duration-500 ease-out bg-gradient-to-r from-teal-500 via-emerald-400 to-emerald-500 shadow-[0_0_14px_rgba(16,185,129,0.5)]';
                            progressBar.style.width = '100%';
                        }
                        if (percentMetrics) {
                            percentMetrics.textContent = '100%';
                            percentMetrics.className = 'text-emerald-400 font-bold font-mono text-xs';
                        }
                        if (countMetrics) {
                            countMetrics.textContent = `${totalSent} / ${totalSent} DELIVERED`;
                        }
                        if (failoverCount > 0 && failoverBadge && failoverCountEl) {
                            failoverBadge.classList.remove('hidden');
                            failoverCountEl.textContent = `${failoverCount} FAILOVER RESCUE`;
                        } else if (failoverBadge) {
                            failoverBadge.classList.add('hidden');
                        }

                        if (progressText) {
                            progressText.className = 'text-emerald-300 font-medium truncate';
                            const rescueNote = (failoverCount > 0) ? ` (${failoverCount} in-flight failover rescue)` : '';
                            progressText.textContent = `All queued dispatches acknowledged by remote peer${rescueNote}.`;
                        }
                        if (latencyMetric) {
                            latencyMetric.textContent = 'ACK: 250 2.0.0 • 100% SUCCESS';
                        }

                        const terminalReport = (failoverCount > 0)
                            ? `[DISPATCH:CONFIRMED] Outbound cycle finished successfully: 100% delivered (${totalSent}/${totalSent} acknowledged, ${failoverCount} rescued via backup relay pipeline).`
                            : `[DISPATCH:CONFIRMED] Outbound cycle finished successfully: 100% delivered (${totalSent}/${totalSent} acknowledged).`;
                        renderTerminalLogLine(terminalReport);
                        setEngineStatus('idle');
                    }
                } else {
                    isFlushingActive = false;
                    if (btn) btn.disabled = false;
                    if (icon) icon.className = 'fas fa-paper-plane text-xs';
                    setEngineStatus('idle');
                }
            } catch (err) {
                isFlushingActive = false;
                if (btn) btn.disabled = false;
                if (icon) icon.className = 'fas fa-paper-plane text-xs';
                renderTerminalLogLine('[ERROR] Transmission interrupted: ' + err.message);
                setEngineStatus('idle');
            }
        }

        processBatch();
    }

    function triggerRetryFailed() {
        enqueueTerminalLogs('[RETRY] Resetting failed queue records to active status...');
        fetch('/queue/retry-failed', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            enqueueTerminalLogs(data.logs || [data.message]);
            refreshQueueMetrics();
        })
        .catch(err => enqueueTerminalLogs(['[ERROR] Failed to retry queue: ' + err.message]));
    }

    function triggerClearQueue() {
        if (!confirm('Are you sure you want to clear all pending emails in the dispatch queue?')) return;

        enqueueTerminalLogs('[PURGE] Purging pending transmission queue records...');
        fetch('/queue/clear', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            enqueueTerminalLogs(data.logs || [data.message]);
            refreshQueueMetrics();
        })
        .catch(err => enqueueTerminalLogs(['[ERROR] Queue purge failed: ' + err.message]));
    }

    function renderTerminalLogLine(message, type = 'info') {
        const term = document.getElementById('studioTerminalLogs');
        if (!term) return;

        const now = new Date();
        const timeStr = now.toLocaleTimeString() + '.' + String(now.getMilliseconds()).padStart(3, '0');
        const line = document.createElement('div');
        line.className = 'flex items-start space-x-2 py-0.5 select-text hover:bg-white/[0.04] px-1.5 rounded transition-colors font-mono text-2xs sm:text-xs leading-relaxed';

        const timestamp = `<span class="text-zinc-600 shrink-0 select-none font-mono text-3xs">[${timeStr}]</span>`;
        const termStatusEl = document.getElementById('termStatusText');
        const kpiSentEl = document.getElementById('termKpiSent');
        const kpiRescuedEl = document.getElementById('termKpiRescued');
        const kpiErrorsEl = document.getElementById('termKpiErrors');
        const kpiSpeedEl = document.getElementById('termKpiSpeed');

        if (message.includes('[WORKER:') || message.includes('[SYS]')) {
            const body = message.replace(/\[WORKER:[^\]]+\]|\[SYS\]/, '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-indigo-950/80 text-indigo-300 border border-indigo-800 font-bold shrink-0 text-3xs">[DAEMON]</span> <span class="text-indigo-200 font-medium">${escapeHtml(body)}</span>`;
            if (termStatusEl) termStatusEl.textContent = 'worker thread active • processing queue buffer...';
        } else if (message.includes('[PIPELINE]')) {
            const body = message.replace('[PIPELINE]', '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-sky-950/80 text-sky-300 border border-sky-800 font-bold shrink-0 text-3xs">[PIPELINE]</span> <span class="text-sky-200">${escapeHtml(body)}</span>`;
        } else if (message.includes('[AST:COMPILE]') || message.includes('[TEMPLATE]')) {
            const body = message.replace(/\[AST:COMPILE\]|\[TEMPLATE\]/, '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-amber-950/80 text-amber-300 border border-amber-800 font-bold shrink-0 text-3xs">[AST:COMPILE]</span> <span class="text-amber-200">${escapeHtml(body)}</span>`;
            if (termStatusEl) termStatusEl.textContent = 'compiling broadcast AST & token definitions...';
        } else if (message.includes('[TOKEN:EVAL]')) {
            const body = message.replace('[TOKEN:EVAL]', '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-cyan-950/80 text-cyan-300 border border-cyan-800 font-bold shrink-0 text-3xs">[TOKEN:EVAL]</span> <span class="text-cyan-200">${escapeHtml(body)}</span>`;
            if (termStatusEl) termStatusEl.textContent = 'evaluating dynamic lead tokens...';
        } else if (message.includes('[PAYLOAD:STORE]')) {
            const body = message.replace('[PAYLOAD:STORE]', '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-blue-950/80 text-blue-300 border border-blue-800 font-bold shrink-0 text-3xs">[STAGED]</span> <span class="text-blue-200">${escapeHtml(body)}</span>`;
        } else if (message.includes('[QUEUE:LOCKED]') || message.includes('[ENQUEUE]')) {
            const body = message.replace(/\[QUEUE:LOCKED\]|\[ENQUEUE\]/, '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-emerald-950/80 text-emerald-300 border border-emerald-800 font-bold shrink-0 text-3xs">[QUEUE:LOCKED]</span> <span class="text-emerald-200 font-semibold">${escapeHtml(body)}</span>`;
            if (termStatusEl) termStatusEl.textContent = 'queue locked in DB buffer. socket dispatch ready.';
        } else if (message.includes('[QUEUE:SKIP]')) {
            const body = message.replace('[QUEUE:SKIP]', '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-zinc-800 text-zinc-400 border border-zinc-700 font-bold shrink-0 text-3xs">[QUEUE:SKIP]</span> <span class="text-zinc-400">${escapeHtml(body)}</span>`;
        } else if (message.includes('[DISPATCH:')) {
            const match = message.match(/\[DISPATCH:([^\]]+)\]/);
            const tag = match ? match[1] : '0xTX';
            const body = message.replace(/\[DISPATCH:[^\]]+\]/, '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-cyan-950/80 text-cyan-300 border border-cyan-700 font-bold shrink-0 text-3xs">[DISPATCH:${tag}]</span> <span class="text-zinc-100 font-semibold">${escapeHtml(body)}</span>`;
            if (termStatusEl) termStatusEl.textContent = `routing lead ${tag}...`;
        } else if (message.includes('[DNS:RESOLVE]')) {
            const body = message.replace('[DNS:RESOLVE]', '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-sky-950/60 text-sky-400 border border-sky-800/80 font-bold shrink-0 text-3xs">[DNS:RESOLVE]</span> <span class="text-sky-300">${escapeHtml(body)}</span>`;
            if (termStatusEl) termStatusEl.textContent = 'resolving gateway MX/TCP address...';
        } else if (message.includes('[SOCKET:TCP]') || message.includes('[SOCKET]')) {
            const body = message.replace(/\[SOCKET:TCP\]|\[SOCKET\]/, '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-blue-950/70 text-blue-300 border border-blue-700 font-bold shrink-0 text-3xs">[SOCKET:TCP]</span> <span class="text-blue-200">${escapeHtml(body)}</span>`;
            if (termStatusEl) termStatusEl.textContent = 'establishing non-blocking TCP socket...';
        } else if (message.includes('[TLS:1.3]') || message.includes('[HANDSHAKE]')) {
            const body = message.replace(/\[TLS:1.3\]|\[HANDSHAKE\]/, '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-purple-950/70 text-purple-300 border border-purple-700 font-bold shrink-0 text-3xs">[TLS:1.3]</span> <span class="text-purple-200">${escapeHtml(body)}</span>`;
            if (termStatusEl) termStatusEl.textContent = 'negotiating TLS cipher suite...';
        } else if (message.includes('[SMTP:AUTH]') || message.includes('[AUTH]')) {
            const body = message.replace(/\[SMTP:AUTH\]|\[AUTH\]/, '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-yellow-950/70 text-yellow-300 border border-yellow-700 font-bold shrink-0 text-3xs">[SMTP:AUTH]</span> <span class="text-yellow-200">${escapeHtml(body)}</span>`;
            if (termStatusEl) termStatusEl.textContent = 'exchanging AUTH LOGIN credentials...';
        } else if (message.includes('[MIME:STREAM]') || message.includes('[TX]')) {
            const body = message.replace(/\[MIME:STREAM\]|\[TX\]/, '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-teal-950/70 text-teal-300 border border-teal-700 font-bold shrink-0 text-3xs">[MIME:STREAM]</span> <span class="text-teal-200">${escapeHtml(body)}</span>`;
            if (termStatusEl) termStatusEl.textContent = 'streaming RFC 2822 payload to socket...';
        } else if (message.includes('[250:ACK]') || message.includes('[ACK]')) {
            const body = message.replace(/\[250:ACK\]|\[ACK\]/, '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-emerald-950/80 text-emerald-300 border border-emerald-600 font-bold shrink-0 text-3xs">[250:ACK]</span> <span class="text-emerald-300 font-medium">${escapeHtml(body)}</span>`;
            if (termStatusEl) termStatusEl.textContent = '250 OK acknowledged by remote peer.';
        } else if (message.includes('[DB:PERSIST]')) {
            const body = message.replace('[DB:PERSIST]', '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-zinc-800/90 text-zinc-300 border border-zinc-700 font-bold shrink-0 text-3xs">[DB:PERSIST]</span> <span class="text-zinc-400">${escapeHtml(body)}</span>`;
        } else if (message.includes('[RELAY:REJECT]') || message.includes('[RELAY FAILED') || message.includes('[RELAY ERROR')) {
            const body = message.replace(/\[RELAY:REJECT\]|\[RELAY FAILED[^\]]*\]|\[RELAY ERROR[^\]]*\]/, '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-amber-950 text-amber-300 border border-amber-700 font-bold shrink-0 text-3xs">[RELAY:REJECT]</span> <span class="text-amber-200 font-semibold bg-amber-950/40 px-1 rounded">${escapeHtml(body)}</span>`;
            if (termStatusEl) termStatusEl.textContent = 'relay rejection detected • initiating failover...';
        } else if (message.includes('[RELAY:ERROR]')) {
            liveErrorsCounter++;
            if (kpiErrorsEl) kpiErrorsEl.textContent = liveErrorsCounter;
            const body = message.replace('[RELAY:ERROR]', '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-rose-950 text-rose-300 border border-rose-700 font-bold shrink-0 text-3xs">[RELAY:ERROR]</span> <span class="text-rose-200 font-semibold">${escapeHtml(body)}</span>`;
            if (termStatusEl) termStatusEl.textContent = 'no active relay gateway available...';
        } else if (message.includes('[DIAGNOSTIC]')) {
            const body = message.replace('[DIAGNOSTIC]', '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-amber-950/80 text-amber-300 border border-amber-700 font-bold shrink-0 text-3xs">[DIAGNOSTIC]</span> <span class="text-amber-200">${escapeHtml(body)}</span>`;
        } else if (message.includes('[RESOLUTION]') || message.includes('[ACTION:REQUIRED]')) {
            const tag = message.includes('[ACTION:REQUIRED]') ? 'ACTION:REQUIRED' : 'RESOLUTION';
            const body = message.replace(/\[RESOLUTION\]|\[ACTION:REQUIRED\]/, '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-amber-950/90 text-amber-200 border border-amber-600 font-bold shrink-0 text-3xs">[${tag}]</span> <span class="text-amber-100 font-semibold">${escapeHtml(body)}</span>`;
        } else if (message.includes('[FAILOVER:ENGAGED]') || message.includes('[FAILOVER')) {
            liveRescuedCounter++;
            if (kpiRescuedEl) kpiRescuedEl.textContent = liveRescuedCounter;
            const body = message.replace(/\[FAILOVER:ENGAGED\]|\[FAILOVER[^\]]*\]/, '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-fuchsia-950 text-fuchsia-200 border border-fuchsia-600 font-bold shrink-0 text-3xs">[FAILOVER]</span> <span class="text-fuchsia-200 font-bold">${escapeHtml(body)}</span>`;
            if (termStatusEl) termStatusEl.textContent = 'circuit-breaker tripped • rerouting in-flight...';
        } else if (message.includes('[DELIVERED]')) {
            liveTxCounter++;
            if (kpiSentEl) kpiSentEl.textContent = liveTxCounter;
            if (batchStartTime) {
                const elapsedSec = (Date.now() - batchStartTime) / 1000;
                if (elapsedSec > 0 && kpiSpeedEl) {
                    const speed = (liveTxCounter / elapsedSec).toFixed(1);
                    kpiSpeedEl.textContent = `${speed} msg/s`;
                }
            }
            const body = message.replace(/\[DELIVERED\]/, '').trim();
            const isRecovery = body.includes('[FAILOVER:RESCUED]') || body.includes('(Recovered');
            if (isRecovery) {
                line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-emerald-950 text-emerald-300 border border-emerald-600 font-bold shrink-0 text-3xs">[DELIVERED]</span> <span class="text-emerald-300 font-semibold">${escapeHtml(body.replace('[FAILOVER:RESCUED]', '').trim())}</span> <span class="px-1 py-0.2 rounded bg-fuchsia-950/80 text-fuchsia-300 text-4xs font-bold border border-fuchsia-700">[RESCUED:FAILOVER]</span>`;
            } else {
                line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-emerald-950 text-emerald-300 border border-emerald-600 font-bold shrink-0 text-3xs">[DELIVERED]</span> <span class="text-emerald-300">${escapeHtml(body)}</span>`;
            }
            if (termStatusEl) termStatusEl.textContent = 'confirmed delivery ledger recorded.';
        } else if (message.includes('[RELAYS:EXHAUSTED]') || message.includes('[ALL RELAYS EXHAUSTED')) {
            liveErrorsCounter++;
            if (kpiErrorsEl) kpiErrorsEl.textContent = liveErrorsCounter;
            const body = message.replace(/\[RELAYS:EXHAUSTED\]|\[ALL RELAYS EXHAUSTED[^\]]*\]/, '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-red-950 text-red-400 border border-red-700 font-black shrink-0 text-3xs">[RELAYS:EXHAUSTED]</span> <span class="text-red-300 font-bold">${escapeHtml(body)}</span>`;
            if (termStatusEl) termStatusEl.textContent = 'all gateway pipelines exhausted for recipient.';
        } else if (message.includes('[BATCH:SUCCESS]') || message.includes('[BATCH:PARTIAL]') || message.includes('[BATCH:FAILED]') || message.includes('[BATCH DONE]')) {
            const isFailed = message.includes('[BATCH:FAILED]');
            const isSuccess = message.includes('[BATCH:SUCCESS]');
            const tag = isFailed ? 'BATCH:FAILED' : (isSuccess ? 'BATCH:SUCCESS' : 'BATCH:PARTIAL');
            const colorClass = isFailed ? 'bg-rose-950 text-rose-300 border-rose-700' : 'bg-amber-950 text-amber-300 border-amber-600';
            const body = message.replace(/\[BATCH:[^\]]+\]|\[BATCH DONE\]/, '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded ${colorClass} font-bold shrink-0 text-3xs">[${tag}]</span> <span class="text-zinc-100 font-semibold">${escapeHtml(body)}</span>`;
            if (termStatusEl) termStatusEl.textContent = isFailed ? 'batch execution terminated with errors.' : 'batch transmission cycle completed.';
        } else if (message.includes('[DISPATCH:SUCCESS]') || message.includes('[DISPATCH:CONFIRMED]') || message.includes('[DISPATCH:ABORTED]') || message.includes('[DISPATCH:WARNING]') || message.includes('[COMPLETE]')) {
            const isAborted = message.includes('[DISPATCH:ABORTED]');
            const isWarn = message.includes('[DISPATCH:WARNING]');
            const isConfirmed = message.includes('[DISPATCH:CONFIRMED]');
            const tag = isAborted ? 'DISPATCH:ABORTED' : (isWarn ? 'DISPATCH:WARNING' : (isConfirmed ? 'DISPATCH:CONFIRMED' : 'DISPATCH:SUCCESS'));
            const colorClass = isAborted ? 'bg-rose-950 text-rose-300 border-rose-700' : (isWarn ? 'bg-amber-950 text-amber-300 border-amber-600' : 'bg-emerald-950 text-emerald-300 border-emerald-600');
            const body = message.replace(/\[DISPATCH:[^\]]+\]|\[COMPLETE\]|\[BROADCAST COMPLETED\]/, '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded ${colorClass} font-bold shrink-0 text-3xs">[${tag}]</span> <span class="text-zinc-100 font-semibold">${escapeHtml(body)}</span>`;
            if (termStatusEl) termStatusEl.textContent = isAborted ? 'dispatch aborted due to errors.' : 'transmission confirmed • worker idle.';
        } else if (message.includes('[BROADCAST:SENT]')) {
            const body = message.replace('[BROADCAST:SENT]', '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-emerald-950 text-emerald-300 border border-emerald-600 font-bold shrink-0 text-3xs">[BROADCAST:SENT]</span> <span class="text-emerald-200 font-semibold">${escapeHtml(body)}</span>`;
        } else if (message.includes('[BROADCAST:WARNING]')) {
            const body = message.replace('[BROADCAST:WARNING]', '').trim();
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-amber-950 text-amber-300 border border-amber-700 font-bold shrink-0 text-3xs">[BROADCAST:WARN]</span> <span class="text-amber-200">${escapeHtml(body)}</span>`;
        } else if (message.includes('[START]') || message.includes('[CORE]') || message.includes('[SYSTEM]')) {
            const tag = message.includes('[CORE]') ? 'CORE' : 'SYSTEM';
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-sky-950 text-sky-300 border border-sky-700 font-bold shrink-0 text-3xs">[${tag}]</span> <span class="text-sky-200 font-semibold">${escapeHtml(message.replace(/\[START\]|\[CORE\]|\[SYSTEM\]/, '').trim())}</span>`;
        } else if (message.includes('[ERROR]') || type === 'error') {
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-rose-950 text-rose-300 border border-rose-700 font-bold shrink-0 text-3xs">[ERROR]</span> <span class="text-rose-300">${escapeHtml(message.replace('[ERROR]', '').trim())}</span>`;
        } else if (message.includes('[WARN]') || type === 'warning') {
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-amber-950 text-amber-300 border border-amber-700 font-bold shrink-0 text-3xs">[WARN]</span> <span class="text-amber-200">${escapeHtml(message.replace('[WARN]', '').trim())}</span>`;
        } else {
            let color = 'text-zinc-300';
            if (type === 'success') color = 'text-emerald-400 font-semibold';
            line.innerHTML = `${timestamp} <span class="px-1.5 py-0.2 rounded bg-zinc-800 text-zinc-400 border border-zinc-700 font-bold shrink-0 text-3xs">[INFO]</span> <span class="${color}">${escapeHtml(message)}</span>`;
        }

        term.appendChild(line);

        // 1. Internal auto-scroll inside the terminal container
        if (autoScrollTerminal) {
            requestAnimationFrame(() => {
                term.scrollTop = term.scrollHeight;
            });
        }

        // 2. Viewport auto-scroll: As the terminal gets down or streams, keep it in the user's viewport
        if (autoScrollTerminal) {
            const termCard = document.getElementById('studioTerminalCard');
            if (termCard) {
                const rect = termCard.getBoundingClientRect();
                const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
                const bottomMargin = 30; // 30px breathing room from window bottom
                if (rect.bottom > viewportHeight - bottomMargin) {
                    const scrollDistance = rect.bottom - (viewportHeight - bottomMargin);
                    window.scrollBy({ top: scrollDistance, behavior: 'smooth' });
                }
            }
        }
    }

    function clearTerminal() {
        const term = document.getElementById('studioTerminalLogs');
        if (term) {
            const now = new Date();
            const timeStr = now.toLocaleTimeString() + '.' + String(now.getMilliseconds()).padStart(3, '0');
            term.innerHTML = `<div class="text-zinc-500 flex items-center space-x-2 py-0.5 font-mono text-2xs sm:text-xs"><span class="text-zinc-600">[${timeStr}]</span> <span class="text-zinc-400 font-bold">[CLEARED]</span> <span>Terminal buffer reset. Worker thread standby.</span></div>`;
        }
        liveTxCounter = 0;
        liveRescuedCounter = 0;
        liveErrorsCounter = 0;
        batchStartTime = null;
        const kpiSentEl = document.getElementById('termKpiSent');
        const kpiRescuedEl = document.getElementById('termKpiRescued');
        const kpiErrorsEl = document.getElementById('termKpiErrors');
        const kpiSpeedEl = document.getElementById('termKpiSpeed');
        if (kpiSentEl) kpiSentEl.textContent = '0';
        if (kpiRescuedEl) kpiRescuedEl.textContent = '0';
        if (kpiErrorsEl) kpiErrorsEl.textContent = '0';
        if (kpiSpeedEl) kpiSpeedEl.textContent = '0.0 msg/s';
        setEngineStatus('idle');
    }

    function copyTerminalLogs() {
        const term = document.getElementById('studioTerminalLogs');
        if (term) {
            navigator.clipboard.writeText(term.innerText)
                .then(() => alert('Terminal logs copied to clipboard!'))
                .catch(() => alert('Could not copy terminal text.'));
        }
    }

    function toggleAutoScroll() {
        autoScrollTerminal = !autoScrollTerminal;
        const btn = document.getElementById('toggleScrollBtn');
        if (btn) {
            btn.innerHTML = `<i class="fas fa-arrow-down text-4xs"></i> <span>Auto-scroll: ${autoScrollTerminal ? 'ON' : 'OFF'}</span>`;
            btn.classList.toggle('text-amber-400', autoScrollTerminal);
            btn.classList.toggle('text-zinc-500', !autoScrollTerminal);
        }
    }

    function refreshQueueMetrics() {
        fetch('/queue/status')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const inQueueEl = document.getElementById('studioInQueue');
                    const sentTodayEl = document.getElementById('studioSentToday');
                    const failedEl = document.getElementById('studioFailed');

                    if (inQueueEl) inQueueEl.textContent = data.in_queue;
                    if (sentTodayEl) sentTodayEl.textContent = data.sent_today;
                    if (failedEl) failedEl.textContent = data.failed_queue;
                }
            })
            .catch(() => {});
    }

    function escapeHtml(string) {
        return String(string).replace(/[&<>"'`=\/]/g, function (s) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
                '/': '&#x2F;',
                '`': '&#x60;',
                '=': '&#x3D;'
            }[s];
        });
    }
</script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                </span>
                <h2 class="font-extrabold text-xl text-zinc-900 dark:text-white tracking-tight flex items-center gap-2">
                    <i class="fas fa-layer-group text-amber-500 text-sm"></i>
                    <span>{{ __('Broadcast Dispatch Studio') }}</span>
                </h2>
                <span class="inline-flex items-center px-2 py-0.2 rounded-full text-4xs font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/80 uppercase">
                    Cockpit View
                </span>
            </div>

            <!-- Compact Header KPIs & Shortcuts -->
            <div class="flex items-center flex-wrap gap-2">
                <div class="flex items-center gap-1.5 text-3xs">
                    <div class="px-2.5 py-1 bg-white dark:bg-[#111114] rounded-lg border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-1.5 shadow-2xs">
                        <span class="text-zinc-400 font-bold uppercase text-4xs">Queue:</span>
                        <span id="studioInQueue" class="font-mono font-extrabold text-blue-600 dark:text-blue-400">{{ $inQueue }}</span>
                    </div>
                    <div class="px-2.5 py-1 bg-white dark:bg-[#111114] rounded-lg border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-1.5 shadow-2xs">
                        <span class="text-zinc-400 font-bold uppercase text-4xs">Sent:</span>
                        <span id="studioSentToday" class="font-mono font-extrabold text-emerald-600 dark:text-emerald-400">{{ $sentToday }}</span>
                    </div>
                    <div class="px-2.5 py-1 bg-white dark:bg-[#111114] rounded-lg border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-1.5 shadow-2xs">
                        <span class="text-zinc-400 font-bold uppercase text-4xs">Failed:</span>
                        <span id="studioFailed" class="font-mono font-extrabold text-rose-600 dark:text-rose-400">{{ $failedCount }}</span>
                    </div>
                </div>

                <a href="/emails" class="inline-flex items-center px-2.5 py-1 bg-white dark:bg-[#111114] text-zinc-700 dark:text-zinc-200 text-3xs font-semibold rounded-lg border border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-all shadow-2xs gap-1">
                    <i class="fas fa-list-check text-4xs text-amber-500"></i>
                    <span>Logs</span>
                </a>
                <a href="/newsletters" class="inline-flex items-center px-2.5 py-1 bg-white dark:bg-[#111114] text-zinc-700 dark:text-zinc-200 text-3xs font-semibold rounded-lg border border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-all shadow-2xs gap-1">
                    <i class="fas fa-paper-plane text-4xs text-amber-500"></i>
                    <span>Broadcasts</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="pt-2.5 pb-4">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-3">

            <!-- Compact Streamlined 4-Stage Stepper Ribbon -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 text-xs select-none">
                <!-- Step 1 -->
                <button type="button" id="stepNav1" onclick="goToStage(1)" class="px-3 py-2 rounded-xl bg-white dark:bg-[#111114] border-2 border-amber-400 dark:border-amber-400 shadow-sm flex items-center space-x-2 cursor-pointer transition-all ring-2 ring-amber-400/20 text-left">
                    <span id="stepBadge1" class="w-5 h-5 rounded-lg bg-amber-500 text-zinc-950 font-black flex items-center justify-center text-3xs shrink-0 shadow-2xs">
                        1
                    </span>
                    <div class="min-w-0">
                        <span class="block text-4xs font-extrabold text-amber-600 dark:text-amber-400 uppercase tracking-wider leading-none">Stage 1</span>
                        <span class="font-bold text-xs text-zinc-900 dark:text-white truncate block">Select Broadcast</span>
                    </div>
                </button>

                <!-- Step 2 -->
                <button type="button" id="stepNav2" onclick="goToStage(2)" class="px-3 py-2 rounded-xl bg-zinc-50/70 dark:bg-[#0d0d10]/70 border border-zinc-200/70 dark:border-zinc-800/60 opacity-60 flex items-center space-x-2 cursor-not-allowed select-none transition-all text-left">
                    <span id="stepBadge2" class="w-5 h-5 rounded-lg bg-zinc-200 dark:bg-zinc-800 text-zinc-400 font-bold flex items-center justify-center text-3xs shrink-0">
                        2
                    </span>
                    <div class="min-w-0">
                        <span class="block text-4xs font-bold text-zinc-400 uppercase tracking-wider leading-none">Stage 2</span>
                        <span class="font-bold text-xs text-zinc-900 dark:text-white truncate block">Audience Review</span>
                    </div>
                </button>

                <!-- Step 3 -->
                <button type="button" id="stepNav3" onclick="goToStage(3)" class="px-3 py-2 rounded-xl bg-zinc-50/70 dark:bg-[#0d0d10]/70 border border-zinc-200/70 dark:border-zinc-800/60 opacity-60 flex items-center space-x-2 cursor-not-allowed select-none transition-all text-left">
                    <span id="stepBadge3" class="w-5 h-5 rounded-lg bg-zinc-200 dark:bg-zinc-800 text-zinc-400 font-bold flex items-center justify-center text-3xs shrink-0">
                        3
                    </span>
                    <div class="min-w-0">
                        <span class="block text-4xs font-bold text-zinc-400 uppercase tracking-wider leading-none">Stage 3</span>
                        <span class="font-bold text-xs text-zinc-900 dark:text-white truncate block">Personalization Check</span>
                    </div>
                </button>

                <!-- Step 4 -->
                <button type="button" id="stepNav4" onclick="goToStage(4)" class="px-3 py-2 rounded-xl bg-zinc-50/70 dark:bg-[#0d0d10]/70 border border-zinc-200/70 dark:border-zinc-800/60 opacity-60 flex items-center space-x-2 cursor-not-allowed select-none transition-all text-left">
                    <span id="stepBadge4" class="w-5 h-5 rounded-lg bg-zinc-200 dark:bg-zinc-800 text-zinc-400 font-bold flex items-center justify-center text-3xs shrink-0">
                        4
                    </span>
                    <div class="min-w-0">
                        <span class="block text-4xs font-bold text-zinc-400 uppercase tracking-wider leading-none">Stage 4</span>
                        <span class="font-bold text-xs text-zinc-900 dark:text-white truncate block">Queue &amp; Dispatch</span>
                    </div>
                </button>
            </div>

            <!-- ======================================================== -->
            <!-- STAGE 1: BROADCAST & SENDER SELECTION PANEL -->
            <!-- ======================================================== -->
            <div id="stagePanel1" class="bg-white dark:bg-[#111114] rounded-2xl p-5 shadow-xs border border-zinc-200/80 dark:border-zinc-800/80 space-y-4">
                <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800/80 pb-3">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 dark:bg-amber-400/10 dark:text-amber-300 border border-amber-500/20 flex items-center justify-center text-xs shrink-0">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-zinc-900 dark:text-white">Stage 1: Broadcast &amp; Sender Relays</h3>
                            <p class="text-3xs text-zinc-500 dark:text-zinc-400">Choose broadcast sequence and inspect outbound SMTP gateway readiness.</p>
                        </div>
                    </div>
                    <a id="stage1EditComposerBtn" href="{{ $selectedNewsletter ? '/newsletter-form/' . $selectedNewsletter->id : '#' }}" target="_blank" class="inline-flex items-center px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-3xs font-semibold rounded-lg hover:bg-zinc-200 transition-colors gap-1">
                        <i class="fas fa-pen-to-square text-4xs"></i>
                        <span>Edit in Composer</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                    <!-- Left: Broadcast Selector & Overview Card -->
                    <div class="lg:col-span-7 space-y-3">
                        <div>
                            <label class="block font-bold text-3xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1" for="studioNewsletterSelect">
                                Active Newsletter Sequence <span class="text-amber-500">*</span>
                            </label>
                            <select id="studioNewsletterSelect" onchange="onNewsletterSelected(this)" class="w-full px-3 py-2 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-[#09090B] text-zinc-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-amber-400/80 focus:border-amber-400 shadow-2xs transition-all">
                                @foreach($newsletters as $n)
                                    <option value="{{ $n->id }}" @if($selectedNewsletter && $selectedNewsletter->id == $n->id) selected @endif>
                                        {{ $n->title }} — [{{ $n->campaign->name ?? 'General Campaign' }}] (Status: {{ $n->status }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Compact Active Broadcast Details Card -->
                        <div class="p-3.5 rounded-xl bg-zinc-50/70 dark:bg-zinc-900/40 border border-zinc-200/70 dark:border-zinc-800/70 space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <div class="font-extrabold text-xs text-zinc-900 dark:text-white truncate" id="summaryStage1Title">
                                    {{ $selectedNewsletter->title ?? 'Untitled Broadcast' }}
                                </div>
                                <span class="flex items-center gap-1 font-medium text-3xs text-zinc-500 dark:text-zinc-400 shrink-0">
                                    <i class="fas fa-folder text-amber-500 text-4xs"></i>
                                    <span id="summaryStage1Campaign">{{ $selectedNewsletter->campaign->name ?? 'General Campaign' }}</span>
                                </span>
                            </div>

                            <div class="p-2.5 bg-white dark:bg-[#111114] rounded-lg border border-zinc-200/80 dark:border-zinc-800/80 text-3xs">
                                <span class="block text-4xs font-bold text-zinc-400 uppercase tracking-wider mb-0.5">Subject Line Template</span>
                                <div id="summaryStage1Subject" class="font-mono text-zinc-800 dark:text-zinc-200 truncate font-semibold">
                                    {{ $selectedNewsletter->subject_template ?? '(No subject template configured)' }}
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-wrap text-3xs">
                                <span class="font-bold text-zinc-400 uppercase text-4xs shrink-0">Target Tags:</span>
                                <div id="summaryStage1Tags" class="flex flex-wrap gap-1">
                                    @if($selectedNewsletter && $selectedNewsletter->newsletter_tags && count($selectedNewsletter->newsletter_tags) > 0)
                                        @foreach($selectedNewsletter->newsletter_tags as $nt)
                                            @if($nt->tag)
                                                <span class="inline-flex items-center px-2 py-0.2 rounded-full text-3xs font-semibold bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/80 dark:border-amber-500/30">
                                                    {{ $nt->tag->label }}
                                                </span>
                                            @endif
                                        @endforeach
                                    @else
                                        <span class="text-zinc-400 italic text-3xs">All active contacts (no tag constraints)</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Outbound Mail Relays -->
                    <div class="lg:col-span-5 space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block font-bold text-3xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Sender SMTP &amp; API Gateways
                            </label>
                            <a href="/mail-accounts" class="text-4xs text-amber-600 dark:text-amber-400 font-bold hover:underline">Manage Gateways &rarr;</a>
                        </div>

                        <div class="space-y-1.5 max-h-40 overflow-y-auto pr-1">
                            @forelse($outboundAccounts as $acc)
                                <div class="p-2.5 rounded-lg bg-zinc-50/70 dark:bg-[#09090B] border border-zinc-200/80 dark:border-zinc-800/80 flex items-center justify-between text-xs hover:border-amber-500/30 transition-colors">
                                    <div class="flex items-center gap-2 truncate min-w-0">
                                        <span class="relative flex h-2 w-2 shrink-0">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                        </span>
                                        <div class="truncate">
                                            <div class="font-bold text-xs text-zinc-900 dark:text-white truncate">{{ $acc->name }}</div>
                                            <div class="text-4xs font-mono text-zinc-400 truncate">{{ $acc->from_email ?? $acc->user_name ?? 'Relay sender' }}</div>
                                        </div>
                                    </div>
                                    <span class="text-4xs font-mono font-bold px-1.5 py-0.5 rounded bg-white dark:bg-[#141417] text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 uppercase shrink-0">
                                        {{ $acc->type }}
                                    </span>
                                </div>
                            @empty
                                <div class="p-3 bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 rounded-lg text-3xs border border-rose-200 dark:border-rose-900/50 flex items-center gap-2">
                                    <i class="fas fa-triangle-exclamation"></i>
                                    <span>No active mail gateways found. <a href="/account-form/new" class="underline font-bold">Add SMTP Relay</a></span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Stage 1 Footer -->
                <div class="flex items-center justify-end pt-3 border-t border-zinc-100 dark:border-zinc-800/80">
                    <button type="button" onclick="goToStage(2)" class="inline-flex items-center px-5 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-bold rounded-xl shadow-xs transition-all gap-1.5 cursor-pointer">
                        <span>Confirm Broadcast &amp; Review Audience Leads</span>
                        <i class="fas fa-arrow-right text-3xs"></i>
                    </button>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- STAGE 2: AUDIENCE RECIPIENT CONTROL PANEL -->
            <!-- ======================================================== -->
            <div id="stagePanel2" class="hidden bg-white dark:bg-[#111114] rounded-2xl p-5 shadow-xs border border-zinc-200/80 dark:border-zinc-800/80 space-y-3">
                <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800/80 pb-2.5">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 dark:bg-amber-400/10 dark:text-amber-300 border border-amber-500/20 flex items-center justify-center text-xs shrink-0">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-extrabold text-zinc-900 dark:text-white">Stage 2: Audience Review &amp; Exclusions</h3>
                                <span id="audienceCountBadge" class="px-2 py-0.2 rounded-full text-3xs font-mono font-bold bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-200 border border-amber-200 dark:border-amber-500/30">
                                    Resolving...
                                </span>
                            </div>
                            <p class="text-3xs text-zinc-500 dark:text-zinc-400">Toggle exclusion checkboxes or edit lead attributes before transmitting.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="selectAllContacts(true)" class="px-2.5 py-1 text-3xs font-bold rounded-lg bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 transition-colors flex items-center gap-1 cursor-pointer">
                            <i class="fas fa-check-square text-amber-500"></i>
                            <span>All</span>
                        </button>
                        <button type="button" onclick="selectAllContacts(false)" class="px-2.5 py-1 text-3xs font-bold rounded-lg bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 transition-colors flex items-center gap-1 cursor-pointer">
                            <i class="fas fa-square text-zinc-400"></i>
                            <span>None</span>
                        </button>
                    </div>
                </div>

                <!-- Search & Status Strip -->
                <div class="flex items-center justify-between gap-3 bg-zinc-50/70 dark:bg-zinc-900/40 px-3 py-1.5 rounded-xl border border-zinc-200/80 dark:border-zinc-800/80">
                    <div class="relative flex-1 max-w-sm">
                        <i class="fas fa-search absolute left-2.5 top-2 text-zinc-400 text-4xs"></i>
                        <input type="text" id="audienceSearchInput" oninput="renderAudienceTable()" placeholder="Search leads by name, email, or company..." 
                               class="w-full pl-7 pr-2 py-1 bg-white dark:bg-[#09090B] border border-zinc-200 dark:border-zinc-800 rounded-lg text-3xs text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-1 focus:ring-amber-400">
                    </div>
                    <div id="selectedLeadCountDisplay" class="text-3xs font-mono text-zinc-600 dark:text-zinc-300 font-medium">
                        Calculating...
                    </div>
                </div>

                <!-- Recipient Table Container with Compact Height -->
                <div class="overflow-x-auto max-h-64 overflow-y-auto border border-zinc-200/80 dark:border-zinc-800/80 rounded-xl">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="sticky top-0 z-10 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-[#09090B] text-4xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                <th class="py-2 pl-4 pr-2 w-8">Send</th>
                                <th class="py-2 px-3">Lead / Recipient</th>
                                <th class="py-2 px-3">Company</th>
                                <th class="py-2 px-3">Tags</th>
                                <th class="py-2 pl-2 pr-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="audienceTableBody" class="divide-y divide-zinc-100 dark:divide-zinc-800/80">
                        </tbody>
                    </table>
                    <div id="audienceLoadingState" class="p-6 text-center text-zinc-400">
                        <i class="fas fa-circle-notch fa-spin text-base mb-1 text-amber-500"></i>
                        <p class="text-3xs font-semibold">Resolving audience segment leads...</p>
                    </div>
                </div>

                <!-- Stage 2 Footer -->
                <div class="flex items-center justify-between pt-2.5 border-t border-zinc-100 dark:border-zinc-800/80">
                    <button type="button" onclick="goToStage(1)" class="px-3.5 py-1.5 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-semibold rounded-xl transition-colors cursor-pointer">
                        &larr; Back
                    </button>
                    <button type="button" onclick="goToStage(3)" class="inline-flex items-center px-5 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-bold rounded-xl shadow-xs transition-all gap-1.5 cursor-pointer">
                        <span>Approve Leads &amp; Inspect Preview</span>
                        <i class="fas fa-arrow-right text-3xs"></i>
                    </button>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- STAGE 3: LIVE PERSONALIZATION PREVIEW (DESKTOP + MOBILE + MAILBOX POPUP) -->
            <!-- ======================================================== -->
            <div id="stagePanel3" class="hidden bg-white dark:bg-[#111114] rounded-2xl p-5 shadow-xs border border-zinc-200/80 dark:border-zinc-800/80 space-y-3">
                <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800/80 pb-2.5 flex-wrap gap-2">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 dark:bg-amber-400/10 dark:text-amber-300 border border-amber-500/20 flex items-center justify-center text-xs shrink-0">
                            <i class="fas fa-wand-magic-sparkles"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-zinc-900 dark:text-white">Stage 3: Desktop &amp; Mobile Device Mockups</h3>
                            <p class="text-3xs text-zinc-500 dark:text-zinc-400">Inspect personalized email rendering across desktop email clients, mobile frames, and authentic mailbox view.</p>
                        </div>
                    </div>

                    <!-- Device Switcher + Full Mailbox Popup Trigger + Lead Navigation Controls -->
                    <div class="flex items-center flex-wrap gap-2">
                        <!-- Fullscreen Gmail Simulator Popup Trigger -->
                        <button type="button" onclick="openFullMailboxModal()" title="Open Fullscreen Authentic Gmail Inbox Simulator" class="px-3 py-1.5 rounded-lg text-3xs font-bold bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-zinc-300 dark:hover:border-zinc-600 text-zinc-800 dark:text-zinc-100 shadow-xs hover:shadow-sm transition-all flex items-center gap-2 cursor-pointer hover:-translate-y-0.5">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 48 48">
                                <path fill="#4285F4" d="M45,16.2l-5,2.75V39a2,2,0,0,1-2,2H34V22.25L24,16,14,22.25V41H10a2,2,0,0,1-2-2V18.95l-5-2.75A2,2,0,0,1,2,14.45V10A2,2,0,0,1,5.08,8.27L24,19.25,42.92,8.27A2,2,0,0,1,46,10v4.45A2,2,0,0,1,45,16.2Z"/>
                                <path fill="#34A853" d="M14,41V22.25L24,16l10,6.25V41a2,2,0,0,1-2,2H16A2,2,0,0,1,14,41Z" opacity="0.2"/>
                                <path fill="#EA4335" d="M42.92,8.27,24,19.25,5.08,8.27A2,2,0,0,1,3,10v1.5l21,12.25,21-12.25V10A2,2,0,0,1,42.92,8.27Z"/>
                            </svg>
                            <span>Open in Gmail Inbox</span>
                            <span class="px-1.5 py-0.5 rounded text-4xs font-bold bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800/80">Fullscreen</span>
                        </button>

                        <!-- Desktop vs Mobile Device Mode Switcher -->
                        <div class="flex items-center gap-1 bg-zinc-100 dark:bg-zinc-900 p-0.5 rounded-lg border border-zinc-200/70 dark:border-zinc-800">
                            <button type="button" id="btnDeviceMode_desktop" onclick="setDeviceMode('desktop')" title="Desktop Webmail View" class="device-toggle-btn px-2.5 py-1 rounded-md text-3xs font-bold bg-amber-500 text-zinc-950 shadow-2xs transition-all flex items-center gap-1 cursor-pointer">
                                <i class="fas fa-desktop text-4xs"></i>
                                <span>Desktop</span>
                            </button>
                            <button type="button" id="btnDeviceMode_mobile" onclick="setDeviceMode('mobile')" title="Mobile Smartphone View" class="device-toggle-btn px-2.5 py-1 rounded-md text-3xs font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-all flex items-center gap-1 cursor-pointer">
                                <i class="fas fa-mobile-screen text-4xs"></i>
                                <span>Mobile</span>
                            </button>
                            <button type="button" id="btnDeviceMode_split" onclick="setDeviceMode('split')" title="Side-by-Side Split View" class="device-toggle-btn px-2.5 py-1 rounded-md text-3xs font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-all flex items-center gap-1 cursor-pointer">
                                <i class="fas fa-table-columns text-4xs"></i>
                                <span>Split</span>
                            </button>
                        </div>

                        <!-- Lead Nav Controls -->
                        <div class="flex items-center gap-1">
                            <button type="button" onclick="navigatePreview(-1)" title="Previous Lead" class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 text-zinc-700 dark:text-zinc-300 flex items-center justify-center text-3xs transition-colors cursor-pointer">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <select id="previewContactSelector" onchange="renderPersonalizedPreview(this.value)" class="px-2 py-1 bg-zinc-50 dark:bg-[#09090B] border border-zinc-200 dark:border-zinc-800 rounded-lg text-3xs font-semibold text-zinc-800 dark:text-zinc-200 max-w-[180px]">
                            </select>
                            <button type="button" onclick="navigatePreview(1)" title="Next Lead" class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 text-zinc-700 dark:text-zinc-300 flex items-center justify-center text-3xs transition-colors cursor-pointer">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Main Device Mockup Frames Container (Responsive Wrapper) -->
                <div id="previewViewWrapper" class="block">
                    <!-- ============================================ -->
                    <!-- 1. DESKTOP EMAIL CLIENT WINDOW MOCKUP -->
                    <!-- ============================================ -->
                    <div id="previewDesktopContainer" class="rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-[#0d0d10] shadow-sm">
                        <!-- Desktop Client Top Header Strip with Zoom Buttons -->
                        <div class="bg-zinc-100 dark:bg-[#141417] px-3 py-1.5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center space-x-2">
                                <span class="w-2 h-2 rounded-full bg-rose-500/80"></span>
                                <span class="w-2 h-2 rounded-full bg-amber-500/80"></span>
                                <span class="w-2 h-2 rounded-full bg-emerald-500/80"></span>
                                <span class="text-4xs font-mono text-zinc-400 ml-1.5 flex items-center gap-1">
                                    <i class="fas fa-laptop text-zinc-500 text-4xs"></i>
                                    <span>Desktop Webmail &bull; <span id="previewLeadCounter">Lead 1</span></span>
                                </span>
                            </div>

                            <!-- Zoom Toolbar Controls -->
                            <div id="previewZoomControlsBar" class="flex items-center space-x-1.5 bg-zinc-200/60 dark:bg-zinc-900/90 px-2 py-0.5 rounded-lg border border-zinc-300/60 dark:border-zinc-700/60">
                                <span class="text-4xs font-bold text-zinc-500 uppercase flex items-center gap-1">
                                    <i class="fas fa-magnifying-glass text-4xs text-amber-500"></i> Zoom:
                                </span>
                                <button type="button" onclick="zoomOut()" title="Zoom Out" class="w-5 h-5 rounded hover:bg-zinc-300 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-300 flex items-center justify-center text-3xs cursor-pointer font-bold">
                                    -
                                </button>
                                <span id="previewZoomLevelText" class="text-3xs font-mono font-bold text-zinc-800 dark:text-zinc-200 min-w-[32px] text-center">80%</span>
                                <button type="button" onclick="zoomIn()" title="Zoom In" class="w-5 h-5 rounded hover:bg-zinc-300 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-300 flex items-center justify-center text-3xs cursor-pointer font-bold">
                                    +
                                </button>
                                <span class="text-zinc-300 dark:text-zinc-700">|</span>
                                <button type="button" data-zoom="0.65" onclick="setPreviewZoom(0.65)" class="zoom-preset-btn px-1.5 py-0.2 rounded text-3xs font-semibold text-zinc-500 hover:text-zinc-900">
                                    65%
                                </button>
                                <button type="button" data-zoom="0.8" onclick="setPreviewZoom(0.8)" class="zoom-preset-btn px-1.5 py-0.2 rounded text-3xs font-bold bg-amber-500 text-zinc-950 shadow-2xs">
                                    80%
                                </button>
                                <button type="button" data-zoom="1.0" onclick="setPreviewZoom(1.0)" class="zoom-preset-btn px-1.5 py-0.2 rounded text-3xs font-semibold text-zinc-500 hover:text-zinc-900">
                                    100%
                                </button>
                            </div>

                            <!-- Rendered vs Source Tab Switcher -->
                            <div class="flex items-center gap-1 bg-zinc-200/70 dark:bg-zinc-900 p-0.5 rounded-lg">
                                <button type="button" id="tabBtnRendered" onclick="switchPreviewTab('rendered')" class="px-2.5 py-0.5 rounded text-3xs font-bold bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white shadow-2xs transition-all">
                                    HTML Rendered
                                </button>
                                <button type="button" id="tabBtnRaw" onclick="switchPreviewTab('raw')" class="px-2.5 py-0.5 rounded text-3xs font-semibold text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-all">
                                    Source Code
                                </button>
                            </div>
                        </div>

                        <!-- Recipient & Subject Header -->
                        <div class="px-3.5 py-2 bg-zinc-50/60 dark:bg-zinc-900/30 border-b border-zinc-200/80 dark:border-zinc-800/80 space-y-1 text-xs">
                            <div id="previewRecipientMeta"></div>
                            <div class="flex items-center gap-1.5 truncate">
                                <span class="text-4xs font-extrabold uppercase tracking-wider text-zinc-400 shrink-0">Subject:</span>
                                <div id="previewRenderedSubject" class="font-bold text-xs text-zinc-900 dark:text-white truncate">
                                    (No Subject)
                                </div>
                            </div>
                        </div>

                        <!-- Body Content Frame with Zoom Container -->
                        <div class="p-3 bg-zinc-100/50 dark:bg-[#070709] overflow-hidden">
                            <div id="previewRenderedBodyPane" class="overflow-y-auto max-h-[38vh] p-4 bg-white text-zinc-900 rounded-lg border border-zinc-200/80 shadow-2xs">
                                <div id="previewRenderedBody" class="origin-top transition-transform duration-200"></div>
                            </div>
                            <div id="previewRawBodyPane" class="hidden font-mono text-3xs text-zinc-700 dark:text-zinc-300 max-h-[38vh] overflow-y-auto whitespace-pre-wrap bg-zinc-50 dark:bg-[#09090B] p-3 rounded-lg border border-zinc-200 dark:border-zinc-800">
                                <code id="previewRawBody"></code>
                            </div>
                        </div>

                        <!-- Merge Tags Replaced Bottom Capsule -->
                        <div class="px-3 py-1.5 bg-zinc-50/80 dark:bg-zinc-900/50 border-t border-zinc-200/80 dark:border-zinc-800/80 flex items-center flex-wrap gap-2 text-3xs">
                            <span class="font-bold uppercase text-4xs text-zinc-400 flex items-center gap-1">
                                <i class="fas fa-tags text-amber-500"></i> Evaluated:
                            </span>
                            <div id="previewTokensDetected" class="flex flex-wrap gap-1"></div>
                        </div>
                    </div>

                    <!-- ============================================ -->
                    <!-- 2. REALISTIC SMARTPHONE DEVICE MOCKUP -->
                    <!-- ============================================ -->
                    <div id="previewMobileContainer" class="hidden">
                        <div class="relative w-[320px] sm:w-[340px] bg-zinc-900 dark:bg-black rounded-[42px] p-3 border-4 border-zinc-700/80 dark:border-zinc-800 shadow-2xl ring-1 ring-zinc-900/40">
                            <!-- Dynamic Island / Speaker Pill -->
                            <div class="w-24 h-4 bg-black rounded-full mx-auto mb-1.5 flex items-center justify-center space-x-2 select-none shadow-inner">
                                <span class="w-2 h-2 rounded-full bg-zinc-800"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-zinc-900"></span>
                            </div>

                            <!-- iOS Status Bar -->
                            <div class="px-3 pb-1.5 flex items-center justify-between text-4xs font-bold text-zinc-800 dark:text-zinc-200 select-none">
                                <span>9:41</span>
                                <div class="flex items-center space-x-1.5 text-4xs">
                                    <i class="fas fa-signal"></i>
                                    <i class="fas fa-wifi"></i>
                                    <i class="fas fa-battery-full text-3xs"></i>
                                </div>
                            </div>

                            <!-- Mobile App Navigation Header -->
                            <div class="px-3 py-1.5 bg-zinc-100 dark:bg-zinc-900 border-y border-zinc-200 dark:border-zinc-800 flex items-center justify-between text-xs">
                                <div class="flex items-center space-x-1 text-amber-600 dark:text-amber-400 font-bold text-3xs">
                                    <i class="fas fa-chevron-left text-4xs"></i>
                                    <span>Inbox</span>
                                </div>
                                <span class="text-4xs font-mono text-zinc-400 font-semibold uppercase tracking-wider">Mobile View</span>
                                <div class="w-4"></div>
                            </div>

                            <!-- Mobile Email Subject & Sender Strip -->
                            <div class="p-2.5 bg-white dark:bg-zinc-950 border-b border-zinc-100 dark:border-zinc-800/80 space-y-1">
                                <div id="previewMobileSubject" class="font-extrabold text-xs text-zinc-900 dark:text-white truncate">
                                    (No Subject)
                                </div>
                                <div id="previewMobileRecipient" class="text-4xs text-zinc-500 truncate"></div>
                            </div>

                            <!-- Mobile Body Viewport Frame -->
                            <div class="bg-white text-zinc-900 p-3 h-[34vh] max-h-[34vh] overflow-y-auto rounded-b-[28px] text-xs shadow-inner">
                                <div id="previewMobileRenderedBody" class="origin-top" style="zoom: 0.85;"></div>
                            </div>

                            <!-- Home Gesture Indicator -->
                            <div class="pt-2 pb-0.5 flex justify-center select-none">
                                <div class="w-28 h-1 bg-zinc-400 dark:bg-zinc-600 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stage 3 Footer -->
                <div class="flex items-center justify-between pt-2.5 border-t border-zinc-100 dark:border-zinc-800/80">
                    <button type="button" onclick="goToStage(2)" class="px-3.5 py-1.5 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-semibold rounded-xl transition-colors cursor-pointer">
                        &larr; Back
                    </button>
                    <button type="button" onclick="goToStage(4)" class="inline-flex items-center px-5 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-bold rounded-xl shadow-xs transition-all gap-1.5 cursor-pointer">
                        <span>Lock &amp; Proceed to Queue &amp; Dispatch</span>
                        <i class="fas fa-arrow-right text-3xs"></i>
                    </button>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- STAGE 4: QUEUE STAGING & LIVE TRANSMISSION ENGINE -->
            <!-- ======================================================== -->
            <div id="stagePanel4" class="hidden bg-white dark:bg-[#111114] rounded-2xl p-5 shadow-xs border border-zinc-200/80 dark:border-zinc-800/80 space-y-3">
                <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800/80 pb-2.5">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 dark:bg-amber-400/10 dark:text-amber-300 border border-amber-500/20 flex items-center justify-center text-xs shrink-0">
                            <i class="fas fa-terminal"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-zinc-900 dark:text-white">Stage 4: Queue Staging &amp; Live Batch Dispatch</h3>
                            <p class="text-3xs text-zinc-500 dark:text-zinc-400">Generate queue records for selected leads, then transmit in real-time socket batches.</p>
                        </div>
                    </div>

                    <span id="stage4TargetCount" class="px-2.5 py-0.5 rounded-full text-3xs font-bold bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-200 border border-amber-200 dark:border-amber-500/30">
                        0 Leads Selected
                    </span>
                </div>

                <!-- 2-Step Execution Controls (Compact Grid) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Phase 4A Card -->
                    <div class="p-3.5 rounded-xl bg-zinc-50/70 dark:bg-zinc-900/40 border border-zinc-200/80 dark:border-zinc-800/80 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-4xs font-extrabold uppercase tracking-wider text-amber-600 dark:text-amber-400">Step 4A • Personalize &amp; Queue</span>
                            <span class="text-4xs text-zinc-400 font-mono">DB Record Storage</span>
                        </div>
                        <p class="text-3xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            Generate queue records for the <span id="stage4TotalCount" class="font-bold text-zinc-900 dark:text-white">0</span> selected leads.
                        </p>
                        <button type="button" id="btnStageQueue" onclick="triggerCustomQueue();" class="w-full inline-flex items-center justify-center px-3 py-2 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-white dark:text-zinc-950 text-xs font-bold rounded-xl shadow-xs transition-all gap-1.5 cursor-pointer disabled:opacity-50">
                            <i id="iconStageQueue" class="fas fa-layer-group text-3xs text-amber-400 dark:text-amber-500"></i>
                            <span>Stage Leads to Queue</span>
                        </button>
                    </div>

                    <!-- Phase 4B Card -->
                    <div class="p-3.5 rounded-xl bg-zinc-50/70 dark:bg-zinc-900/40 border border-zinc-200/80 dark:border-zinc-800/80 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-4xs font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Step 4B • Socket Transmission</span>
                            <span class="text-4xs text-zinc-400 font-mono">SMTP Socket Stream</span>
                        </div>
                        <p class="text-3xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            Stream batch delivery through your active gateways with live socket feedback.
                        </p>
                        <button type="button" id="btnStudioFlush" onclick="triggerStudioFlush();" class="w-full inline-flex items-center justify-center px-3 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow-xs transition-all gap-1.5 cursor-pointer disabled:opacity-50">
                            <i id="iconStudioFlush" class="fas fa-paper-plane text-3xs"></i>
                            <span>Dispatch Live Batch Now</span>
                        </button>
                    </div>
                </div>

                <!-- Utilities Strip -->
                <div class="flex items-center justify-between gap-3 p-2 bg-zinc-50/50 dark:bg-zinc-900/20 rounded-xl border border-zinc-200/60 dark:border-zinc-800/60">
                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="triggerRetryFailed();" class="px-2.5 py-1 bg-white dark:bg-zinc-800 hover:bg-zinc-100 text-zinc-700 dark:text-zinc-200 text-3xs font-semibold rounded-lg border border-zinc-200 dark:border-zinc-700 transition-colors gap-1 flex items-center cursor-pointer shadow-2xs">
                            <i class="fas fa-arrows-rotate text-4xs text-amber-500"></i>
                            <span>Retry Failed</span>
                        </button>
                        <button type="button" onclick="triggerClearQueue();" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 text-3xs font-semibold rounded-lg border border-rose-200 dark:border-rose-900/50 transition-colors gap-1 flex items-center cursor-pointer">
                            <i class="fas fa-trash-can text-4xs"></i>
                            <span>Purge Queue</span>
                        </button>
                    </div>
                    <div class="flex items-center gap-2 text-4xs font-mono text-zinc-500">
                        <span>PIPELINE ROUTE: <strong class="text-zinc-700 dark:text-zinc-300 font-semibold">AUTOMATIC FAILOVER</strong></span>
                    </div>
                </div>

                <!-- High-Tech Transmission Telemetry & Progress Monitor -->
                <div id="dispatchProgressContainer" class="hidden flex flex-col gap-2.5 p-3.5 bg-zinc-900/90 dark:bg-[#0c0e14] border border-zinc-700/60 dark:border-zinc-800/80 rounded-xl shadow-xl backdrop-blur-md transition-all duration-300">
                    <!-- Telemetry Header -->
                    <div class="flex items-center justify-between gap-2 flex-wrap">
                        <div class="flex items-center gap-2">
                            <span id="dispatchStatusBadge" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-sky-950/80 text-sky-300 border border-sky-700/70 font-mono text-3xs font-bold uppercase tracking-wider">
                                <span id="dispatchStatusDot" class="w-1.5 h-1.5 rounded-full bg-sky-400 animate-pulse"></span>
                                <span id="dispatchStatusTitle">INITIALIZING TRANSMISSION STREAM</span>
                            </span>

                            <span id="dispatchFailoverBadge" class="hidden inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-fuchsia-950/80 text-fuchsia-200 border border-fuchsia-700/70 font-mono text-3xs font-bold">
                                <i class="fas fa-shield-halved text-4xs text-fuchsia-400"></i>
                                <span id="dispatchFailoverCount">1 FAILOVER RESCUE</span>
                            </span>
                        </div>

                        <!-- Numerical Progress & Percentage -->
                        <div class="flex items-center gap-2 font-mono text-3xs">
                            <span id="dispatchCountMetrics" class="text-zinc-400 font-semibold tracking-wider">0 / 0 DELIVERED</span>
                            <span class="text-zinc-600">&bull;</span>
                            <span id="dispatchPercentMetrics" class="text-emerald-400 font-bold font-mono text-xs">0%</span>
                        </div>
                    </div>

                    <!-- High-Precision Progress Track -->
                    <div class="w-full bg-black/60 rounded-full h-2.5 p-0.5 border border-zinc-800/90 shadow-inner overflow-hidden flex items-center">
                        <div id="dispatchProgressBar" class="h-full rounded-full transition-all duration-500 ease-out bg-gradient-to-r from-teal-500 via-emerald-400 to-emerald-500 shadow-[0_0_12px_rgba(16,185,129,0.4)]" style="width: 0%;"></div>
                    </div>

                    <!-- Technical Operational Subtext -->
                    <div class="flex items-center justify-between text-4xs font-mono text-zinc-400 pt-0.5">
                        <div class="flex items-center gap-2 truncate">
                            <span class="text-zinc-500 uppercase tracking-widest text-5xs font-bold shrink-0">LEDGER:</span>
                            <span id="dispatchProgressText" class="text-zinc-300 font-medium truncate">Outbound queue locked • Initializing socket stream...</span>
                        </div>
                        <div class="flex items-center gap-2 text-zinc-500 shrink-0">
                            <span id="dispatchLatencyMetric">ACK: 250 2.0.0</span>
                        </div>
                    </div>
                </div>

                <!-- Live Streaming CLI Terminal Console -->
                <div id="studioTerminalCard" class="rounded-2xl overflow-hidden border border-zinc-800 bg-[#07080c] shadow-2xl font-mono text-xs flex flex-col transition-all duration-300 relative">
                    <!-- Neon Header Glow Accent -->
                    <div class="h-[2px] w-full bg-gradient-to-r from-amber-500/40 via-emerald-500/60 to-indigo-500/40"></div>

                    <!-- High-Tech Daemon Titlebar -->
                    <div class="bg-[#101217] px-3.5 py-2.5 border-b border-zinc-800/80 flex items-center justify-between select-none flex-wrap gap-2">
                        <div class="flex items-center space-x-2.5">
                            <div class="flex items-center space-x-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#ff5f56] shadow-2xs hover:brightness-125 cursor-pointer"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-[#ffbd2e] shadow-2xs hover:brightness-125 cursor-pointer"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-[#27c93f] shadow-2xs hover:brightness-125 cursor-pointer"></span>
                            </div>
                            <span class="text-3xs font-mono text-zinc-300 ml-1.5 flex items-center gap-1.5 font-medium">
                                <i class="fas fa-terminal text-4xs text-amber-400"></i>
                                <span>campaignstack-core[v2.5] ~ worker-01:pid:{{ getmypid() }} (TCP:587/TLS)</span>
                            </span>
                        </div>

                        <!-- Live Telemetry Inline Counter Chips + Controls -->
                        <div class="flex items-center space-x-2.5 text-3xs font-mono">
                            <div class="hidden sm:flex items-center space-x-1.5 text-4xs border-r border-zinc-800 pr-2.5">
                                <span class="px-1.5 py-0.5 rounded bg-emerald-950/60 text-emerald-400 border border-emerald-800/60 font-semibold">
                                    TX: <strong id="termKpiSent" class="font-black text-emerald-300">0</strong>
                                </span>
                                <span class="px-1.5 py-0.5 rounded bg-fuchsia-950/60 text-fuchsia-300 border border-fuchsia-800/60 font-semibold">
                                    RESCUED: <strong id="termKpiRescued" class="font-black text-fuchsia-200">0</strong>
                                </span>
                                <span class="px-1.5 py-0.5 rounded bg-rose-950/60 text-rose-400 border border-rose-800/60 font-semibold">
                                    ERR: <strong id="termKpiErrors" class="font-black text-rose-300">0</strong>
                                </span>
                                <span class="px-1.5 py-0.5 rounded bg-zinc-800/80 text-amber-300 border border-zinc-700/60 font-semibold">
                                    <span id="termKpiSpeed">0.0 msg/s</span>
                                </span>
                            </div>

                            <button type="button" id="toggleScrollBtn" onclick="toggleAutoScroll()" class="px-2 py-0.5 rounded bg-zinc-800/60 text-amber-400 hover:text-amber-300 border border-zinc-700/50 flex items-center gap-1 cursor-pointer transition-colors text-4xs font-bold">
                                <i class="fas fa-arrow-down text-4xs"></i>
                                <span>Auto-scroll: ON</span>
                            </button>
                            <button type="button" onclick="copyTerminalLogs()" class="text-zinc-400 hover:text-white flex items-center gap-1 cursor-pointer transition-colors text-4xs px-1.5 py-0.5">
                                <i class="fas fa-copy text-4xs"></i>
                                <span>Copy</span>
                            </button>
                            <button type="button" onclick="clearTerminal()" class="text-zinc-400 hover:text-white flex items-center gap-1 cursor-pointer transition-colors text-4xs px-1.5 py-0.5">
                                <i class="fas fa-ban text-4xs"></i>
                                <span>Clear</span>
                            </button>
                            <span id="termLiveBadge" class="text-emerald-400 font-bold flex items-center gap-1 text-4xs pl-1.5 border-l border-zinc-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                LIVE
                            </span>
                        </div>
                    </div>

                    <!-- Micro Hardware / Engine Specs HUD Strip -->
                    <div class="bg-[#0b0c10] px-4 py-1.5 border-b border-zinc-800/80 flex items-center justify-between text-4xs font-mono text-zinc-400 select-none flex-wrap gap-2">
                        <div class="flex items-center space-x-3">
                            <span><strong class="text-zinc-500">CORE:</strong> Async Socket Worker</span>
                            <span class="text-zinc-700">&bull;</span>
                            <span><strong class="text-zinc-500">CIPHER:</strong> TLS 1.3 / ECDHE-RSA-AES256</span>
                            <span class="text-zinc-700">&bull;</span>
                            <span><strong class="text-zinc-500">BUFFER:</strong> <span id="termHudBuffer" class="text-amber-400 font-bold">0 Staged</span></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-zinc-500">GATEWAY STREAM:</span>
                            <span id="termHudStatus" class="text-emerald-400 font-semibold flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                                READY / LISTENING
                            </span>
                        </div>
                    </div>

                    <!-- Terminal Monospace Log Stream Body -->
                    <div id="studioTerminalLogs" class="p-4 space-y-1 h-[48vh] max-h-[50vh] overflow-y-auto text-zinc-300 leading-relaxed font-mono text-2xs sm:text-xs select-text scrollbar-thin scrollbar-thumb-zinc-700 bg-[#07080c]">
                        <div class="text-zinc-500 flex items-center space-x-2 py-0.5 font-mono">
                            <span class="text-zinc-600">[{{ now()->format('H:i:s.v') }}]</span>
                            <span class="text-emerald-400 font-bold shrink-0">[INIT]</span>
                            <span>Daemon worker online. TCP socket pool initialized & listening for dispatch events.</span>
                        </div>
                    </div>

                    <!-- Terminal Active Cursor Prompt -->
                    <div class="px-4 py-2 bg-[#0c0d12] border-t border-zinc-800/80 flex items-center justify-between text-4xs font-mono text-zinc-400 select-none">
                        <div class="flex items-center space-x-2 overflow-hidden text-ellipsis whitespace-nowrap">
                            <span class="text-emerald-400 font-bold shrink-0">engine@worker:~$</span>
                            <span id="termStatusText" class="text-zinc-300 truncate">worker idle &amp; awaiting batch command...</span>
                            <span class="w-1.5 h-3.5 bg-emerald-400 animate-pulse inline-block shrink-0"></span>
                        </div>
                        <span class="text-zinc-500 hidden sm:inline shrink-0 pl-2">TTY: /dev/pts/1 &bull; 587/TCP ESTABLISHED</span>
                    </div>
                </div>

                <!-- Stage 4 Footer -->
                <div class="flex items-center justify-between pt-2.5 border-t border-zinc-100 dark:border-zinc-800/80">
                    <button type="button" onclick="goToStage(3)" class="px-3.5 py-1.5 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-semibold rounded-xl transition-colors cursor-pointer">
                        &larr; Back
                    </button>
                    <a href="/emails" class="inline-flex items-center px-4 py-1.5 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-200 text-xs font-bold rounded-xl transition-colors gap-1.5 shadow-2xs">
                        <i class="fas fa-list-check text-amber-500 text-3xs"></i>
                        <span>View Sent Logs Table</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- ======================================================== -->
    <!-- AUTHENTIC FULLSCREEN GMAIL INBOX SIMULATOR MODAL -->
    <!-- Takes complete screen edge-to-edge covering nav and headers -->
    <!-- ======================================================== -->
    <div id="mailboxSimulatorModal" class="fixed inset-0 z-[999999] hidden overflow-hidden w-screen h-screen bg-[#f6f8fc] dark:bg-[#1f1f23] flex flex-col p-0 m-0 select-none">
        <div class="w-full h-full bg-[#f6f8fc] dark:bg-[#1f1f23] flex flex-col overflow-hidden animate-in fade-in duration-150">
            
            <!-- Top Authentic Gmail Navigation Bar (Full Viewport Width) -->
            <div class="h-16 px-4 py-2 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between shrink-0 bg-[#f6f8fc] dark:bg-[#1f1f23]">
                <!-- Left: Hamburger + Gmail 4-Color Logo & Wordmark -->
                <div class="flex items-center space-x-3 shrink-0">
                    <button type="button" class="w-10 h-10 rounded-full flex items-center justify-center text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200/70 dark:hover:bg-zinc-800 cursor-pointer transition-colors" title="Main menu">
                        <i class="fas fa-bars text-sm"></i>
                    </button>
                    <div class="flex items-center space-x-2 select-none">
                        <svg class="w-7 h-7 shrink-0" viewBox="0 0 48 48">
                            <path fill="#4285F4" d="M45,16.2l-5,2.75V39a2,2,0,0,1-2,2H34V22.25L24,16,14,22.25V41H10a2,2,0,0,1-2-2V18.95l-5-2.75A2,2,0,0,1,2,14.45V10A2,2,0,0,1,5.08,8.27L24,19.25,42.92,8.27A2,2,0,0,1,46,10v4.45A2,2,0,0,1,45,16.2Z"/>
                            <path fill="#34A853" d="M14,41V22.25L24,16l10,6.25V41a2,2,0,0,1-2,2H16A2,2,0,0,1,14,41Z" opacity="0.2"/>
                            <path fill="#EA4335" d="M42.92,8.27,24,19.25,5.08,8.27A2,2,0,0,1,3,10v1.5l21,12.25,21-12.25V10A2,2,0,0,1,42.92,8.27Z"/>
                        </svg>
                        <span class="text-[22px] font-normal tracking-tight text-[#444746] dark:text-[#e3e3e3]" style="font-family: 'Product Sans', 'Google Sans', Roboto, sans-serif;">Gmail</span>
                    </div>
                </div>

                <!-- Center: Authentic Gmail Search Bar -->
                <div class="max-w-2xl w-full mx-4 hidden md:flex items-center bg-[#eaf1fb] dark:bg-[#2b2b30] hover:bg-white hover:shadow-md dark:hover:bg-[#333538] focus-within:bg-white focus-within:shadow-md px-4 py-2.5 rounded-full transition-all border border-transparent">
                    <i class="fas fa-search text-zinc-500 dark:text-zinc-400 mr-3 text-sm"></i>
                    <input type="text" readonly value="in:inbox is:unread Campaign Stack Simulation" class="bg-transparent border-0 focus:ring-0 text-xs text-zinc-700 dark:text-zinc-200 placeholder:text-zinc-500 w-full outline-none font-normal cursor-default">
                    <button type="button" class="text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 p-1 cursor-pointer" title="Search options">
                        <i class="fas fa-sliders text-xs"></i>
                    </button>
                </div>

                <!-- Right: Lead Switcher Pill, Google Icons, User Avatar, Exit Fullscreen -->
                <div class="flex items-center space-x-2 shrink-0">
                    <!-- Recipient Lead Switcher in Header -->
                    <div class="flex items-center gap-1 bg-white dark:bg-[#282a2d] px-2 py-1 rounded-full border border-zinc-200 dark:border-zinc-700 shadow-2xs">
                        <span class="text-4xs uppercase tracking-wider font-bold text-zinc-400 pl-1 hidden sm:inline">Lead:</span>
                        <button type="button" onclick="navigatePreview(-1)" title="Previous Lead" class="w-6 h-6 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-300 flex items-center justify-center text-3xs transition-colors cursor-pointer">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <select id="simMailboxLeadSelector" onchange="renderPersonalizedPreview(this.value)" class="px-1.5 py-0.5 bg-transparent border-0 text-3xs font-semibold text-zinc-800 dark:text-zinc-200 max-w-[140px] sm:max-w-[200px] truncate focus:ring-0 outline-none cursor-pointer">
                        </select>
                        <button type="button" onclick="navigatePreview(1)" title="Next Lead" class="w-6 h-6 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-300 flex items-center justify-center text-3xs transition-colors cursor-pointer">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>

                    <!-- Google App Icons -->
                    <button type="button" class="w-9 h-9 rounded-full hover:bg-zinc-200/70 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hidden sm:flex items-center justify-center text-sm cursor-pointer" title="Support">
                        <i class="far fa-circle-question"></i>
                    </button>
                    <button type="button" class="w-9 h-9 rounded-full hover:bg-zinc-200/70 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hidden sm:flex items-center justify-center text-sm cursor-pointer" title="Settings">
                        <i class="fas fa-gear"></i>
                    </button>
                    <button type="button" class="w-9 h-9 rounded-full hover:bg-zinc-200/70 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hidden md:flex items-center justify-center text-sm cursor-pointer" title="Google apps">
                        <i class="fas fa-grip"></i>
                    </button>

                    <!-- Google Account Profile Avatar -->
                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold flex items-center justify-center text-xs shadow-2xs cursor-pointer" title="Google Account: Active Lead Inbox">
                        R
                    </div>

                    <!-- Native Fullscreen Monitor Toggle -->
                    <button type="button" onclick="toggleBrowserFullscreen()" title="Toggle Monitor Fullscreen" class="w-9 h-9 rounded-full hover:bg-zinc-200/70 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hidden sm:flex items-center justify-center text-xs cursor-pointer">
                        <i class="fas fa-expand"></i>
                    </button>

                    <!-- Exit Fullscreen Button -->
                    <button type="button" onclick="closeFullMailboxModal()" title="Exit Fullscreen (Escape)" class="ml-1 sm:ml-2 px-3.5 py-1.5 rounded-full bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white text-xs font-bold flex items-center gap-1.5 transition-all cursor-pointer shadow-xs">
                        <i class="fas fa-compress text-3xs"></i>
                        <span class="hidden sm:inline">Exit Fullscreen</span>
                        <kbd class="text-4xs opacity-60 font-mono bg-white/20 dark:bg-black/20 px-1 py-0.5 rounded">ESC</kbd>
                    </button>
                </div>
            </div>

            <!-- Gmail Body Layout: Left Sidebar + Right Mail Canvas -->
            <div class="flex-1 flex overflow-hidden">
                
                <!-- Left Authentic Gmail Sidebar -->
                <div class="w-56 lg:w-60 bg-[#f6f8fc] dark:bg-[#1f1f23] px-3 py-3 flex flex-col justify-between shrink-0 hidden md:flex select-none border-r border-transparent">
                    <div class="space-y-1">
                        <!-- Compose Button -->
                        <div class="mb-3">
                            <button type="button" class="inline-flex items-center gap-3 bg-[#c2e7ff] hover:bg-[#b3d7ef] hover:shadow-md text-[#001d35] font-semibold text-xs px-5 py-3.5 rounded-2xl shadow-2xs transition-all cursor-pointer">
                                <i class="fas fa-pen text-xs"></i>
                                <span>Compose</span>
                            </button>
                        </div>

                        <!-- Gmail Navigation Categories -->
                        <div class="bg-[#d3e3fd] text-[#041e49] dark:bg-[#004a77] dark:text-[#d3e3fd] font-bold rounded-full px-4 py-2 flex items-center justify-between text-xs cursor-pointer shadow-2xs">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-inbox text-xs"></i>
                                <span>Inbox</span>
                            </div>
                            <span class="text-3xs font-extrabold px-1.5 py-0.5 rounded-full bg-[#041e49]/10 dark:bg-[#d3e3fd]/20">1</span>
                        </div>

                        <div class="hover:bg-zinc-200/50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-medium rounded-full px-4 py-2 flex items-center gap-3 text-xs cursor-pointer transition-colors">
                            <i class="far fa-star text-xs text-zinc-500"></i>
                            <span>Starred</span>
                        </div>

                        <div class="hover:bg-zinc-200/50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-medium rounded-full px-4 py-2 flex items-center gap-3 text-xs cursor-pointer transition-colors">
                            <i class="far fa-clock text-xs text-zinc-500"></i>
                            <span>Snoozed</span>
                        </div>

                        <div class="hover:bg-zinc-200/50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-medium rounded-full px-4 py-2 flex items-center gap-3 text-xs cursor-pointer transition-colors">
                            <i class="far fa-paper-plane text-xs text-zinc-500"></i>
                            <span>Sent</span>
                        </div>

                        <div class="hover:bg-zinc-200/50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-medium rounded-full px-4 py-2 flex items-center gap-3 text-xs cursor-pointer transition-colors">
                            <i class="far fa-file text-xs text-zinc-500"></i>
                            <span>Drafts</span>
                        </div>

                        <div class="hover:bg-zinc-200/50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-medium rounded-full px-4 py-2 flex items-center gap-3 text-xs cursor-pointer transition-colors">
                            <i class="fas fa-chevron-down text-3xs text-zinc-500"></i>
                            <span>More</span>
                        </div>

                        <!-- Labels Section -->
                        <div class="pt-4 border-t border-zinc-200/60 dark:border-zinc-800/60 mt-3 space-y-1">
                            <div class="px-4 py-1 text-4xs font-bold uppercase tracking-wider text-zinc-400 flex items-center justify-between">
                                <span>Labels</span>
                                <i class="fas fa-plus text-3xs hover:text-zinc-700 dark:hover:text-zinc-200 cursor-pointer"></i>
                            </div>
                            <div class="px-4 py-1.5 rounded-full hover:bg-zinc-200/50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs flex items-center gap-2.5 cursor-pointer">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                <span class="truncate">Campaign Stack</span>
                            </div>
                            <div class="px-4 py-1.5 rounded-full hover:bg-zinc-200/50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs flex items-center gap-2.5 cursor-pointer">
                                <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                                <span class="truncate">Direct Outreach</span>
                            </div>
                            <div class="px-4 py-1.5 rounded-full hover:bg-zinc-200/50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs flex items-center gap-2.5 cursor-pointer">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <span class="truncate">VIP Lead</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Storage Meter -->
                    <div class="pt-3 border-t border-zinc-200/60 dark:border-zinc-800/60 px-2 text-3xs text-zinc-500 space-y-1.5">
                        <div class="flex items-center justify-between text-4xs font-medium">
                            <span>0.42 GB of 15 GB used</span>
                            <i class="fas fa-arrow-up-right-from-square text-4xs text-zinc-400"></i>
                        </div>
                        <div class="w-full h-1 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full" style="width: 12%;"></div>
                        </div>
                        <span class="text-4xs text-zinc-400 block">Terms &bull; Privacy &bull; Program Policies</span>
                    </div>
                </div>

                <!-- Right Main Message View (White Gmail Surface) -->
                <div class="flex-1 bg-white dark:bg-[#111114] sm:rounded-tl-2xl shadow-sm border-t border-l border-zinc-200/80 dark:border-zinc-800/80 flex flex-col overflow-hidden">
                    
                    <!-- Gmail Top Action Toolbar -->
                    <div class="px-4 py-2 bg-white dark:bg-[#111114] border-b border-zinc-200/80 dark:border-zinc-800/80 flex items-center justify-between flex-wrap gap-2 text-zinc-600 dark:text-zinc-400 shrink-0">
                        <!-- Standard Gmail Action Buttons -->
                        <div class="flex items-center space-x-1 sm:space-x-2 text-xs">
                            <button type="button" onclick="closeFullMailboxModal()" class="w-8 h-8 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-zinc-700 dark:text-zinc-300 cursor-pointer" title="Back to Studio">
                                <i class="fas fa-arrow-left text-xs"></i>
                            </button>
                            <button type="button" class="w-8 h-8 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-400 cursor-pointer" title="Archive">
                                <i class="fas fa-box-archive text-xs"></i>
                            </button>
                            <button type="button" class="w-8 h-8 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-400 cursor-pointer" title="Report spam">
                                <i class="fas fa-circle-exclamation text-xs"></i>
                            </button>
                            <button type="button" class="w-8 h-8 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-400 cursor-pointer" title="Delete">
                                <i class="fas fa-trash-can text-xs"></i>
                            </button>
                            <span class="text-zinc-300 dark:text-zinc-700 px-1">|</span>
                            <button type="button" class="w-8 h-8 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-400 cursor-pointer" title="Mark as unread">
                                <i class="fas fa-envelope text-xs"></i>
                            </button>
                            <button type="button" class="w-8 h-8 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-400 cursor-pointer" title="Snooze">
                                <i class="fas fa-clock text-xs"></i>
                            </button>
                            <button type="button" class="w-8 h-8 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-400 cursor-pointer hidden sm:flex" title="Add to tasks">
                                <i class="fas fa-circle-check text-xs"></i>
                            </button>
                            <span class="text-zinc-300 dark:text-zinc-700 px-1 hidden sm:inline">|</span>
                            <button type="button" class="w-8 h-8 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-400 cursor-pointer hidden sm:flex" title="Move to">
                                <i class="fas fa-folder-arrow-up text-xs"></i>
                            </button>
                            <button type="button" class="w-8 h-8 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-400 cursor-pointer hidden sm:flex" title="Labels">
                                <i class="fas fa-tag text-xs"></i>
                            </button>
                        </div>

                        <!-- Right: Desktop/Mobile View Switcher + Counter + Pager -->
                        <div class="flex items-center space-x-2.5 text-xs">
                            <!-- In-Modal View Switcher: Desktop vs Mobile -->
                            <div class="flex items-center gap-0.5 bg-zinc-100 dark:bg-zinc-800/80 p-0.5 rounded-full border border-zinc-200 dark:border-zinc-700">
                                <button type="button" id="simBtnDesktop" onclick="setSimDeviceMode('desktop')" title="Desktop Gmail View" class="px-3 py-1 rounded-full text-3xs font-bold bg-[#c2e7ff] dark:bg-[#004a77] text-[#001d35] dark:text-[#c2e7ff] shadow-2xs transition-all flex items-center gap-1.5 cursor-pointer">
                                    <i class="fas fa-desktop text-4xs"></i>
                                    <span>Desktop</span>
                                </button>
                                <button type="button" id="simBtnMobile" onclick="setSimDeviceMode('mobile')" title="Mobile Gmail App View" class="px-3 py-1 rounded-full text-3xs font-semibold text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-all flex items-center gap-1.5 cursor-pointer">
                                    <i class="fas fa-mobile-screen text-4xs"></i>
                                    <span>Mobile</span>
                                </button>
                            </div>

                            <span id="simMailboxCounter" class="text-3xs text-zinc-500 dark:text-zinc-400 font-mono">1 of 1</span>
                            
                            <div class="flex items-center">
                                <button type="button" onclick="navigatePreview(-1)" title="Previous email" class="w-7 h-7 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-400 text-3xs cursor-pointer">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button type="button" onclick="navigatePreview(1)" title="Next email" class="w-7 h-7 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-400 text-3xs cursor-pointer">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Authentic Subject Header Bar -->
                    <div class="px-6 pt-4 pb-2 flex items-center justify-between gap-4 shrink-0 bg-white dark:bg-[#111114]">
                        <div class="flex items-center gap-3 min-w-0">
                            <h1 id="simMailboxSubject" class="text-lg sm:text-xl font-medium text-zinc-900 dark:text-zinc-100 tracking-tight leading-snug truncate" style="font-family: 'Google Sans', Roboto, sans-serif;">
                                (No Subject Line)
                            </h1>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="px-2 py-0.5 rounded text-4xs font-medium bg-zinc-200/70 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                    Inbox
                                </span>
                                <span class="hidden sm:inline-flex items-center px-1.5 py-0.5 rounded text-4xs font-medium bg-amber-100 text-amber-900 dark:bg-amber-950/60 dark:text-amber-200 gap-1">
                                    <i class="fas fa-tag text-5xs"></i>
                                    <span>Important</span>
                                </span>
                            </div>
                        </div>

                        <!-- Header quick actions -->
                        <div class="flex items-center space-x-2 shrink-0">
                            <button type="button" onclick="toggleSimStar(this)" title="Star email" class="w-8 h-8 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-zinc-400 hover:text-amber-400 text-xs transition-colors cursor-pointer">
                                <i class="far fa-star"></i>
                            </button>
                            <button type="button" onclick="window.print()" title="Print all" class="w-8 h-8 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 text-xs transition-colors cursor-pointer">
                                <i class="fas fa-print"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Sender & Recipient Details Card -->
                    <div class="px-6 py-2 bg-white dark:bg-[#111114] border-b border-zinc-100 dark:border-zinc-800/80 shrink-0 relative">
                        <div class="flex items-start justify-between flex-wrap gap-3">
                            <div class="flex items-start space-x-3 min-w-0">
                                <!-- Sender Circular Avatar -->
                                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-rose-500 to-amber-500 text-white font-extrabold flex items-center justify-center text-sm shadow-xs shrink-0 mt-0.5">
                                    {{ substr($outboundAccounts->first()->name ?? 'C', 0, 1) }}
                                </div>
                                
                                <div class="min-w-0 space-y-0.5">
                                    <div class="flex items-center space-x-2 flex-wrap text-xs">
                                        <span class="font-bold text-zinc-900 dark:text-white">{{ $outboundAccounts->first()->name ?? 'Campaign Dispatcher' }}</span>
                                        <span class="text-zinc-500 dark:text-zinc-400 font-mono text-3xs">&lt;{{ $outboundAccounts->first()->from_email ?? 'broadcast@campaign-stack.com' }}&gt;</span>
                                        <a href="#" onclick="event.preventDefault()" class="text-3xs text-blue-600 dark:text-blue-400 hover:underline">Unsubscribe</a>
                                    </div>
                                    
                                    <!-- "to me" button with dropdown arrow -->
                                    <div class="flex items-center space-x-1 text-xs">
                                        <button type="button" onclick="toggleGmailDetails()" class="inline-flex items-center gap-1 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white text-3xs py-0.5 rounded cursor-pointer">
                                            <span>to <strong id="simMailboxRecipient" class="font-semibold text-zinc-800 dark:text-zinc-200">Recipient Lead</strong></span>
                                            <i class="fas fa-caret-down text-4xs"></i>
                                        </button>
                                        <span id="simMailboxRecipientEmail" class="text-3xs font-mono text-zinc-400 hidden sm:inline"></span>
                                        <span class="text-zinc-300 dark:text-zinc-700">•</span>
                                        <span class="inline-flex items-center text-emerald-600 dark:text-emerald-400 gap-1 text-4xs font-medium">
                                            <i class="fas fa-lock text-5xs"></i>
                                            <span>Standard encryption (TLS)</span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Timestamp & Action icons -->
                            <div class="flex items-center space-x-2 text-xs text-zinc-500 dark:text-zinc-400">
                                <span id="simMailboxDate" class="text-3xs font-normal">Today, 10:42 AM</span>
                                <button type="button" class="w-7 h-7 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-zinc-500 cursor-pointer" title="Reply">
                                    <i class="fas fa-reply text-3xs"></i>
                                </button>
                                <button type="button" class="w-7 h-7 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-zinc-500 cursor-pointer" title="More options">
                                    <i class="fas fa-ellipsis-vertical text-3xs"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Expandable Gmail Delivery Details Card -->
                        <div id="simGmailDetailsCard" class="hidden absolute top-full left-6 mt-1 z-30 w-80 sm:w-96 bg-white dark:bg-[#1e1f23] rounded-xl shadow-xl border border-zinc-200 dark:border-zinc-700 p-4 text-xs space-y-2 animate-in fade-in zoom-in-95 duration-100">
                            <div class="grid grid-cols-3 gap-2 text-3xs">
                                <span class="text-zinc-400 font-medium">From:</span>
                                <span class="col-span-2 font-medium text-zinc-800 dark:text-zinc-200">{{ $outboundAccounts->first()->name ?? 'Campaign Dispatcher' }} &lt;{{ $outboundAccounts->first()->from_email ?? 'broadcast@campaign-stack.com' }}&gt;</span>
                                
                                <span class="text-zinc-400 font-medium">To:</span>
                                <span id="simDetailTo" class="col-span-2 font-medium text-zinc-800 dark:text-zinc-200">Recipient Lead</span>
                                
                                <span class="text-zinc-400 font-medium">Date:</span>
                                <span id="simDetailDate" class="col-span-2 font-medium text-zinc-800 dark:text-zinc-200">Today</span>
                                
                                <span class="text-zinc-400 font-medium">Subject:</span>
                                <span id="simDetailSubject" class="col-span-2 font-medium text-zinc-800 dark:text-zinc-200">(No Subject Line)</span>
                                
                                <span class="text-zinc-400 font-medium">Mailed-by:</span>
                                <span class="col-span-2 font-mono text-zinc-600 dark:text-zinc-400">campaign-stack.com</span>

                                <span class="text-zinc-400 font-medium">Security:</span>
                                <span class="col-span-2 text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                    <i class="fas fa-lock text-4xs"></i>
                                    <span>Standard encryption (TLS)</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Email Body Content Viewport (Desktop Iframe vs Mobile Phone Frame) -->
                    <div class="flex-1 overflow-hidden bg-zinc-100/50 dark:bg-[#070709] relative flex flex-col">
                        
                        <!-- Desktop Iframe Viewport (Direct HTML Email Render) -->
                        <div id="simDesktopFrame" class="w-full h-full flex flex-col bg-white">
                            <iframe id="simMailboxIframe" class="w-full h-full border-0 bg-white" title="Email Mailbox Rendered Body"></iframe>
                        </div>

                        <!-- Mobile Smartphone Mockup Frame inside Simulator -->
                        <div id="simMobileFrame" class="hidden w-full h-full overflow-y-auto p-4 sm:p-6 flex justify-center items-center bg-zinc-200/50 dark:bg-[#0a0a0c]">
                            <div class="relative w-[340px] sm:w-[370px] bg-zinc-950 rounded-[48px] p-3.5 border-4 border-zinc-700 shadow-2xl">
                                <!-- Dynamic Island -->
                                <div class="w-24 h-4 bg-black rounded-full mx-auto mb-2 flex items-center justify-center space-x-2">
                                    <span class="w-2 h-2 rounded-full bg-zinc-800"></span>
                                </div>
                                <!-- Mobile Phone Status Bar -->
                                <div class="px-3 pb-1 flex items-center justify-between text-4xs font-bold text-zinc-200">
                                    <span>9:41</span>
                                    <div class="flex items-center space-x-1.5 text-4xs">
                                        <i class="fas fa-signal"></i>
                                        <i class="fas fa-wifi"></i>
                                        <i class="fas fa-battery-full text-3xs"></i>
                                    </div>
                                </div>
                                <!-- Gmail Mobile Header Simulation inside phone -->
                                <div class="bg-[#f6f8fc] dark:bg-[#1f1f23] px-3 py-2 flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 text-xs">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-arrow-left text-zinc-600 text-3xs"></i>
                                        <span class="font-bold text-3xs text-zinc-800 dark:text-zinc-200">Inbox</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-zinc-500 text-3xs">
                                        <i class="fas fa-box-archive"></i>
                                        <i class="fas fa-trash-can"></i>
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                </div>
                                <!-- Mobile Iframe Viewport -->
                                <div class="bg-white rounded-b-[28px] overflow-hidden h-[480px] shadow-inner">
                                    <iframe id="simMailboxMobileIframe" class="w-full h-full border-0 bg-white" title="Mobile Email Mailbox Rendered Body"></iframe>
                                </div>
                                <!-- Mobile Home Indicator Bar -->
                                <div class="pt-2 pb-0.5 flex justify-center">
                                    <div class="w-28 h-1 bg-zinc-600 rounded-full"></div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Bottom Gmail Quick Reply / Forward Card & Reactions -->
                    <div class="px-6 py-3.5 bg-white dark:bg-[#111114] border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between text-xs shrink-0 flex-wrap gap-2">
                        <!-- Reply / Forward buttons (Pill shaped like Gmail) -->
                        <div class="flex items-center space-x-2">
                            <button type="button" class="px-5 py-2 rounded-full border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 text-xs font-medium flex items-center gap-2 transition-all cursor-pointer shadow-2xs">
                                <i class="fas fa-reply text-3xs text-zinc-500"></i>
                                <span>Reply</span>
                            </button>
                            <button type="button" class="px-5 py-2 rounded-full border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 text-xs font-medium flex items-center gap-2 transition-all cursor-pointer shadow-2xs">
                                <i class="fas fa-share text-3xs text-zinc-500"></i>
                                <span>Forward</span>
                            </button>
                            
                            <!-- Quick emoji reactions just like Gmail -->
                            <div class="hidden sm:flex items-center space-x-1 pl-2">
                                <button type="button" class="w-8 h-8 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-sm cursor-pointer transition-transform hover:scale-110" title="Smile">😊</button>
                                <button type="button" class="w-8 h-8 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-sm cursor-pointer transition-transform hover:scale-110" title="Thumbs up">👍</button>
                                <button type="button" class="w-8 h-8 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-sm cursor-pointer transition-transform hover:scale-110" title="Heart">❤️</button>
                                <button type="button" class="w-8 h-8 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center text-sm cursor-pointer transition-transform hover:scale-110" title="Party">🎉</button>
                            </div>
                        </div>

                        <!-- Back to Studio Action Button -->
                        <div class="flex items-center space-x-2">
                            <button type="button" onclick="closeFullMailboxModal()" class="px-4 py-2 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 text-xs font-bold rounded-xl transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                                <i class="fas fa-arrow-left text-3xs"></i>
                                <span>Return to Dispatch Studio</span>
                            </button>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Quick Edit Contact Modal -->
    <div id="quickEditModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-zinc-950/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#141417] rounded-2xl max-w-md w-full p-5 shadow-2xl border border-zinc-200 dark:border-zinc-800 transform transition-all space-y-3.5">
            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-2.5">
                <div class="flex items-center space-x-2">
                    <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs border border-amber-200 dark:border-amber-800/60">
                        <i class="fas fa-pen text-3xs"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-zinc-900 dark:text-white">Edit Lead Before Dispatch</h3>
                        <p class="text-4xs text-zinc-400">Updates will reflect immediately in this run</p>
                    </div>
                </div>
                <button type="button" onclick="closeQuickEditModal()" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-white cursor-pointer">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <form onsubmit="saveQuickEdit(event)" class="space-y-3 text-xs">
                <input type="hidden" id="editContactId">
                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block font-bold text-4xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">First Name</label>
                        <input type="text" id="editFirstname" class="w-full px-3 py-1.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-[#09090B] text-zinc-900 dark:text-white text-xs focus:ring-1 focus:ring-amber-400">
                    </div>
                    <div>
                        <label class="block font-bold text-4xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Last Name</label>
                        <input type="text" id="editLastname" class="w-full px-3 py-1.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-[#09090B] text-zinc-900 dark:text-white text-xs focus:ring-1 focus:ring-amber-400">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-4xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Email Address <span class="text-amber-500">*</span></label>
                    <input type="email" id="editEmail" required class="w-full px-3 py-1.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-[#09090B] text-zinc-900 dark:text-white text-xs focus:ring-1 focus:ring-amber-400">
                </div>

                <div>
                    <label class="block font-bold text-4xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Company / Organization</label>
                    <input type="text" id="editCompany" class="w-full px-3 py-1.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-[#09090B] text-zinc-900 dark:text-white text-xs focus:ring-1 focus:ring-amber-400">
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                    <button type="button" onclick="closeQuickEditModal()" class="px-3 py-1.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-semibold rounded-xl hover:bg-zinc-200 transition-colors cursor-pointer">Cancel</button>
                    <button type="submit" class="px-3.5 py-1.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

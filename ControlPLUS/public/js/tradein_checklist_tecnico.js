(function () {
    "use strict";

    const SCOPE_SELECTOR = '[data-scope="checklist-tecnico"]';
    const FORM_SCOPE_SELECTOR = '[data-tradein-evaluation-form="1"]';
    const RADIO_SELECTOR = ".tech-result-option";
    const TOGGLE_SIM_SELECTOR = '[data-role="checklist-tecnico-all-sim"]';
    const TOGGLE_NAO_SELECTOR = '[data-role="checklist-tecnico-all-nao"]';
    const CONCLUIR_AVALIACAO_SELECTOR = '[data-role="concluir-avaliacao"]';
    const SAVE_BUTTON_SELECTOR = '[data-role="save-avaliacao"]';
    const DOC_BUTTON_SELECTOR = ".btn-tradein-generate-document, .btn-tradein-generate-pdv";

    function normalizeNumber(raw) {
        if (raw === null || typeof raw === "undefined") return null;
        const str = String(raw).trim();
        if (!str) return null;
        const cleaned = str.replace(/[^\d,.-]/g, "");
        if (!cleaned) return null;
        const normalized = cleaned.includes(",")
            ? cleaned.replace(/\./g, "").replace(",", ".")
            : cleaned;
        const value = Number(normalized);
        if (Number.isNaN(value)) return null;
        return value;
    }

    function getFormScope(node) {
        if (!node) return null;
        if (node.matches && node.matches(FORM_SCOPE_SELECTOR)) return node;
        if (node.closest) return node.closest(FORM_SCOPE_SELECTOR);
        return null;
    }

    function getGroupedRadios(scope) {
        const groups = new Map();
        scope.querySelectorAll(RADIO_SELECTOR).forEach((radio) => {
            if (!radio.name) return;
            if (!groups.has(radio.name)) {
                groups.set(radio.name, []);
            }
            groups.get(radio.name).push(radio);
        });
        return groups;
    }

    function updateBulkToggles(scope) {
        const toggleSim = scope.querySelector(TOGGLE_SIM_SELECTOR);
        const toggleNao = scope.querySelector(TOGGLE_NAO_SELECTOR);
        if (!toggleSim || !toggleNao) return;

        const groups = getGroupedRadios(scope);
        if (groups.size === 0) {
            toggleSim.checked = false;
            toggleNao.checked = false;
            return;
        }

        let allSim = true;
        let allNao = true;

        groups.forEach((radios) => {
            const selected = radios.find((radio) => radio.checked);
            if (!selected || selected.value !== "SIM") {
                allSim = false;
            }
            if (!selected || selected.value !== "NAO") {
                allNao = false;
            }
        });

        toggleSim.checked = allSim;
        toggleNao.checked = allNao;
    }

    function applyValueToAll(scope, targetValue) {
        const groups = getGroupedRadios(scope);
        groups.forEach((radios) => {
            const target = radios.find((radio) => radio.value === targetValue);
            if (target) {
                target.checked = true;
            }
        });
        updateBulkToggles(scope);
    }

    function buildValidationResult(scope) {
        const errors = [];
        if (!scope) {
            return {
                ok: false,
                errors: ["Formulário de avaliação não encontrado."],
            };
        }

        const valorAparelhoInput = scope.querySelector('[name="cabecalho[valor_aparelho]"]');
        const valorAvaliadoInput = scope.querySelector('[name="valor_avaliado"]');

        const valorAparelho = normalizeNumber(valorAparelhoInput ? valorAparelhoInput.value : "");
        if (valorAparelho === null || valorAparelho <= 0) {
            errors.push("Informe um valor do aparelho válido (maior que zero).");
        }

        const valorAvaliado = normalizeNumber(valorAvaliadoInput ? valorAvaliadoInput.value : "");
        if (valorAvaliado === null || valorAvaliado < 0) {
            errors.push("Informe um valor avaliado válido (maior ou igual a zero).");
        }

        const concluirAvaliacao = scope.querySelector(CONCLUIR_AVALIACAO_SELECTOR);
        if (!concluirAvaliacao || concluirAvaliacao.disabled || !concluirAvaliacao.checked) {
            errors.push("Marque \"Concluir avaliação\" para salvar.");
        }

        const checklistScope = scope.querySelector(SCOPE_SELECTOR);
        const checklistGroups = checklistScope ? getGroupedRadios(checklistScope) : new Map();
        if (!checklistGroups.size) {
            errors.push("Checklist técnico não encontrado.");
        } else {
            checklistGroups.forEach((radios) => {
                const selected = radios.find((radio) => radio.checked);
                if (!selected) {
                    errors.push("Preencha todos os itens do Checklist Técnico (Sim/Não).");
                }
            });
        }

        const requiredChecklistContainers = scope.querySelectorAll("[data-required-checklist]");
        requiredChecklistContainers.forEach((container) => {
            const groups = new Map();
            container.querySelectorAll('input[type="radio"][name]').forEach((radio) => {
                if (!groups.has(radio.name)) groups.set(radio.name, []);
                groups.get(radio.name).push(radio);
            });

            groups.forEach((radios) => {
                const selected = radios.find((radio) => radio.checked);
                if (!selected) {
                    errors.push("Preencha todos os checklists obrigatórios.");
                }
            });
        });

        scope.querySelectorAll('input[type="checkbox"][required]').forEach((checkbox) => {
            if (!checkbox.checked) {
                errors.push("Marque todos os checkboxes obrigatórios.");
            }
        });

        scope.querySelectorAll("[required]").forEach((field) => {
            const isRadioOrCheckbox = field.type === "radio" || field.type === "checkbox";
            if (isRadioOrCheckbox) return;
            if (typeof field.value === "string" && field.value.trim() === "") {
                errors.push("Preencha todos os campos obrigatórios.");
            }
        });

        return {
            ok: errors.length === 0,
            errors: Array.from(new Set(errors)),
        };
    }

    function isEvaluationSaved(scope) {
        if (!scope) return false;
        return String(scope.getAttribute("data-evaluation-saved") || "0") === "1";
    }

    function updateEvaluationButtonsState(scope) {
        if (!scope) return;

        const canSave = buildValidationResult(scope).ok;
        scope.querySelectorAll(SAVE_BUTTON_SELECTOR).forEach((button) => {
            button.disabled = !canSave;
        });

        const canGenerate = isEvaluationSaved(scope);
        scope.querySelectorAll(DOC_BUTTON_SELECTOR).forEach((button) => {
            if (button.tagName === "A") {
                if (canGenerate) {
                    button.classList.remove("disabled");
                    button.removeAttribute("aria-disabled");
                    if (!button.getAttribute("target")) {
                        button.setAttribute("target", "_blank");
                    }
                } else {
                    button.classList.add("disabled");
                    button.setAttribute("aria-disabled", "true");
                    button.removeAttribute("href");
                    button.removeAttribute("target");
                }
            } else {
                button.disabled = !canGenerate;
            }
        });
    }

    function markEvaluationSaved(scope, saved) {
        const formScope = getFormScope(scope);
        if (!formScope) return;
        formScope.setAttribute("data-evaluation-saved", saved ? "1" : "0");
        updateEvaluationButtonsState(formScope);
    }

    function syncChecklistTecnicoControls(root) {
        const node = root || document;
        node.querySelectorAll(SCOPE_SELECTOR).forEach((scope) => {
            updateBulkToggles(scope);
        });
        node.querySelectorAll(FORM_SCOPE_SELECTOR).forEach((scope) => {
            updateEvaluationButtonsState(scope);
        });
    }

    document.addEventListener("change", function (event) {
        const target = event.target;
        if (!(target instanceof Element)) return;

        const formScope = getFormScope(target);
        const scope = target.closest(SCOPE_SELECTOR);
        if (!scope) {
            if (formScope) {
                updateEvaluationButtonsState(formScope);
            }
            return;
        }

        if (target.matches(TOGGLE_SIM_SELECTOR)) {
            if (target.checked) {
                const toggleNao = scope.querySelector(TOGGLE_NAO_SELECTOR);
                if (toggleNao) toggleNao.checked = false;
                applyValueToAll(scope, "SIM");
            } else {
                updateBulkToggles(scope);
            }
            return;
        }

        if (target.matches(TOGGLE_NAO_SELECTOR)) {
            if (target.checked) {
                const toggleSim = scope.querySelector(TOGGLE_SIM_SELECTOR);
                if (toggleSim) toggleSim.checked = false;
                applyValueToAll(scope, "NAO");
            } else {
                updateBulkToggles(scope);
            }
            return;
        }

        if (target.matches(RADIO_SELECTOR)) {
            updateBulkToggles(scope);
        }

        if (formScope) {
            updateEvaluationButtonsState(formScope);
        }
    });

    document.addEventListener("input", function (event) {
        const target = event.target;
        if (!(target instanceof Element)) return;
        const formScope = getFormScope(target);
        if (formScope) {
            updateEvaluationButtonsState(formScope);
        }
    });

    document.addEventListener("submit", function (event) {
        const target = event.target;
        if (!(target instanceof Element) || !target.matches(FORM_SCOPE_SELECTOR)) return;
        const result = buildValidationResult(target);
        if (!result.ok) {
            event.preventDefault();
            if (window.toastr && result.errors.length) {
                window.toastr.warning(result.errors[0]);
            }
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        syncChecklistTecnicoControls(document);
    });

    if (typeof MutationObserver === "function") {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (!(node instanceof Element)) return;
                    if (node.matches(SCOPE_SELECTOR)) {
                        updateBulkToggles(node);
                        return;
                    }
                    syncChecklistTecnicoControls(node);
                });
            });
        });
        observer.observe(document.documentElement, { childList: true, subtree: true });
    }

    window.syncChecklistTecnicoControls = syncChecklistTecnicoControls;
    window.getTradeinEvaluationValidation = function (scope) {
        return buildValidationResult(getFormScope(scope) || scope || document.querySelector(FORM_SCOPE_SELECTOR));
    };
    window.canSaveEvaluation = function (scope) {
        return window.getTradeinEvaluationValidation(scope).ok;
    };
    window.canGenerateDocuments = function (scope) {
        return isEvaluationSaved(getFormScope(scope) || scope || document.querySelector(FORM_SCOPE_SELECTOR));
    };
    window.markTradeinEvaluationSaved = markEvaluationSaved;
    window.updateTradeinEvaluationState = function (scope) {
        const formScope = getFormScope(scope) || scope || document.querySelector(FORM_SCOPE_SELECTOR);
        if (formScope) {
            updateEvaluationButtonsState(formScope);
        }
    };
})();

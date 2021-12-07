class NorbrFormHandler {

    constructor(formId) {
        this.form = document.getElementById(formId);

        this.enableListeners();
    }

    enableListeners() {
        let _onDomChange = this.onDomChange,
            _this = this;
        let mutationObserver = new MutationObserver(function (args) {
            _onDomChange.apply(_this, [args]);
        })

        mutationObserver.observe(this.form, {childList: true, subtree: false});

        // Small hack to keep the scheme in the form on submit
        let schemeInput = document.getElementById('norbr-customer_scheme_name');
        if (schemeInput) {
            schemeInput.classList.remove('norbr-class');
        }
    }

    onDomChange(mutation) {
        for (let i = 0; i < mutation.length; i++) {
            for (let j = 0; j < mutation[i].addedNodes.length; j++) {
                let node = mutation[i].addedNodes[j];

                if ('norbr-token' !== node.getAttribute('id')) {
                    continue;
                }

                if ('input' !== node.tagName.toLowerCase()) {
                    continue;
                }

                if ('hidden' !== node.getAttribute('type')) {
                    continue;
                }

                if ('' === node.value) {
                    continue;
                }

                this.form.submit();
            }
        }
    }
}

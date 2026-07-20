(() => {

    class Toaster {

        constructor() {

            /* Config */
            this.position = 'top-right';
            this.duration = 3000;
            this.maxVisible = 5;
            this.spacing = 12;

            /* Queue & State */
            this.queue = [];
            this.active = [];
            this.visible = [];
            this.counter = 0;

            this.options = {
                position: 'top-right',
                duration: 3000,
                max: 5,
                html: false
            };

            /* Icons */
            this.icons = {
                success: `<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>`,
                error: `<svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`,
                warning: `<svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18A2 2 0 003.53 21h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>`,
                info: `<svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>`,
                primary: `<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/></svg>`,
                secondary: `<svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/></svg>`,
                dark: `<svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.35 15.04A9 9 0 118.96 3.65 7 7 0 0020.35 15.04z"/></svg>`
            };

            /* Colors */
            this.styles = {
                success: { strip: 'bg-green-500', bg: 'bg-green-50', text: 'text-green-900', progress: 'bg-green-500' },
                error: { strip: 'bg-red-500', bg: 'bg-red-50', text: 'text-red-900', progress: 'bg-red-500' },
                warning: { strip: 'bg-amber-500', bg: 'bg-amber-50', text: 'text-amber-900', progress: 'bg-amber-500' },
                info: { strip: 'bg-sky-500', bg: 'bg-sky-50', text: 'text-sky-900', progress: 'bg-sky-500' },
                primary: { strip: 'bg-blue-600', bg: 'bg-blue-50', text: 'text-blue-900', progress: 'bg-blue-600' },
                secondary: { strip: 'bg-gray-500', bg: 'bg-gray-100', text: 'text-gray-900', progress: 'bg-gray-500' },
                dark: { strip: 'bg-gray-900', bg: 'bg-gray-800', text: 'text-white', progress: 'bg-white' }
            };

            this.createContainer();
        }

        /* Container */
        createContainer() {
            if (!document.body) {
                window.addEventListener('DOMContentLoaded', () => this.createContainer(), {
                    once: true
                });
                return;
            }
            
            if (this.container) {
                this.container.remove();
            }

            this.container = document.createElement('div');
            this.container.className = this.getPositionClasses();
            document.body.appendChild(this.container);
        }

        /* Position */
        getPositionClasses() {
            const base = `fixed z-[999999] flex flex-col gap-3 p-4 pointer-events-none w-full sm:w-auto max-w-md`;
            const map = {
                "top-right": "top-0 right-0 items-end",
                "top-left": "top-0 left-0 items-start",
                "top-middle": "top-0 left-1/2 -translate-x-1/2 items-center",
                "middle-right": "top-1/2 right-0 -translate-y-1/2 items-end",
                "middle-left": "top-1/2 left-0 -translate-y-1/2 items-start",
                "middle-middle": "top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 items-center",
                "bottom-right": "bottom-0 right-0 items-end",
                "bottom-left": "bottom-0 left-0 items-start",
                "bottom-middle": "bottom-0 left-1/2 -translate-x-1/2 items-center"
            };
            return base + ' ' + (map[this.options.position] ?? map["top-right"]);
        }

        getAnimationClass() {
            switch(this.options.position) {
                case 'top-left': return '-translate-x-6';
                case 'middle-left': return '-translate-x-6';
                case 'bottom-left': return '-translate-x-6';
                case 'top-right': return 'translate-x-6';
                case 'middle-right': return 'translate-x-6';
                case 'bottom-right': return 'translate-x-6';
                case 'top-middle': return '-translate-y-6';
                case 'bottom-middle': return 'translate-y-6';
                default: return 'scale-95';
            }
        }

        /* Public API */
        success(message, duration = null) { this.enqueue('success', message, duration); }
        error(message, duration = null) { this.enqueue('error', message, duration); }
        warning(message, duration = null) { this.enqueue('warning', message, duration); }
        info(message, duration = null) { this.enqueue('info', message, duration); }
        primary(message, duration = null) { this.enqueue('primary', message, duration); }
        secondary(message, duration = null) { this.enqueue('secondary', message, duration); }
        dark(message, duration = null) { this.enqueue('dark', message, duration); }

        /* Queue */
        enqueue(type, message, duration) {
            this.queue.push({
                type,
                message,
                duration: duration ?? this.duration
            });
            this.processQueue();
        }

        processQueue() {
            if (this.active.length >= this.maxVisible) return;
            if (this.queue.length === 0) return;

            const toast = this.queue.shift();
            this.showToast(toast);
        }

        /* Show Toast */
        showToast(toast) {
            const theme = this.styles[toast.type];
            const card = document.createElement('div');

            card.className = `
                relative overflow-hidden rounded-xl shadow-xl border border-gray-200 
                ${theme.bg} ${theme.text} pointer-events-auto opacity-0 
                ${this.getAnimationClass()} transition-all duration-300
            `;

            card.innerHTML = `
                <div class="flex">
                    <div class="w-1.5 ${theme.strip}"></div>
                    <div class="flex-1 p-4">
                        <div class="flex items-start gap-3">
                            <div class="text-xl font-bold leading-none mt-0.5">${this.icons[toast.type]}</div>
                            <div class="flex-1 text-sm leading-6 break-words">${toast.message}</div>
                            <button class="toast-close text-gray-400 hover:text-red-600 text-xl leading-none transition">&times;</button>
                        </div>
                    </div>
                </div>
                <div class="absolute left-0 bottom-0 w-full h-1 bg-black/10">
                    <div class="toast-progress h-full ${theme.progress}" style="width:100%"></div>
                </div>
            `;

            this.container.appendChild(card);
            this.active.push(card);

            requestAnimationFrame(() => {
                card.classList.remove('opacity-0');
                card.classList.remove(this.getAnimationClass());
            });

            const progress = card.querySelector('.toast-progress');
            const closeBtn = card.querySelector('.toast-close');
            let duration = toast.duration;
            let start = Date.now();
            let remaining = duration;
            let timer;

            const animate = (time) => {
                const elapsed = time - start;
                const percent = Math.max(0, 100 - (elapsed / duration * 100));
                progress.style.width = percent + '%';
                if (percent > 0) timer = requestAnimationFrame(animate);
            };

            timer = requestAnimationFrame(animate);

            const autoClose = setTimeout(() => {
                cancelAnimationFrame(timer);
                this.removeToast(card);
            }, duration);

            /* Hover Pause */
            card.addEventListener('mouseenter', () => {
                clearTimeout(autoClose);
                cancelAnimationFrame(timer);
                remaining -= Date.now() - start;
            });

            card.addEventListener('mouseleave', () => {
                start = Date.now();
                duration = remaining;
                timer = requestAnimationFrame(animate);
                setTimeout(() => {
                    cancelAnimationFrame(timer);
                    this.removeToast(card);
                }, remaining);
            });

            /* Close Button */
            closeBtn.onclick = () => {
                cancelAnimationFrame(timer);
                this.removeToast(card);
            };
        }

        /* Remove Toast */
        removeToast(card) {
            if (!card) return;

            card.classList.add('opacity-0');
            card.classList.add(this.getAnimationClass());

            setTimeout(() => {
                card.remove();
                this.active = this.active.filter(t => t !== card);
                this.processQueue();
            }, 250);
        }

    }

    window.toaster = new Toaster();

})();
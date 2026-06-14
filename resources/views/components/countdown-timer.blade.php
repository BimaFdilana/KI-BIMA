<div x-data="{
    seconds: {{ $seconds }},
    startingSeconds: {{ $seconds }},
    timer: null,
    formattedTime: '',
    startCountdown() {
        this.seconds = this.startingSeconds;
        clearInterval(this.timer);
        this.timer = setInterval(() => {
            this.seconds--;
            if (this.seconds <= 0) {
                clearInterval(this.timer);
                $dispatch('{{ $event }}');
            }
            this.updateFormattedTime();
        }, 1000);
        this.updateFormattedTime();
    },
    updateFormattedTime() {
        const minutes = Math.floor(this.seconds / 60);
        const remainingSeconds = this.seconds % 60;
        this.formattedTime = `${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`;
    }
}" x-init="updateFormattedTime()" x-on:start-countdown.window="startingSeconds = $event.detail.seconds; startCountdown()" class="inline-flex items-center text-sm text-gray-600">
    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <span x-text="formattedTime"></span>
</div>

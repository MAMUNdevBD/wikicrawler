<div class="text-center container mx-auto flex flex-col items-center py-10">
    {{ $check }}
    @if($error)
    <div class="text-red-600 font-bold">*{{ $error }}</div>
    @endif
    <div class="flex">
        <div class="pl-3">
            <label class="flex flex-col w-72">
                <span class="font-bold text-xl">Country URL:</span>
                <input wire:model="url" type="text" class="py-1 w-full">
            </label>
        </div>
    </div>
    @if($success)
    <div class="text-center">{{ $success }}</div>
    @endif
    <button wire:click="startGrabbing()"
        class="bg-indigo-600 text-white w-full py-1 font-bold mt-5 text-xl">START</button>
</div>

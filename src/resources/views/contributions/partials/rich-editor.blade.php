@props([
    'id' => 'content',
    'name' => 'content',
    'value' => '',
    'maxlength' => 20000,
])

<div class="rich-editor" data-rich-editor>
    <div class="rich-editor-toolbar" aria-label="Rich text controls">
        <button type="button" data-command="bold">B</button>
        <button type="button" data-command="italic">I</button>
        <button type="button" data-command="underline">U</button>
        <button type="button" data-command="insertUnorderedList">List</button>
        <button type="button" data-command="insertOrderedList">1. List</button>
        <button type="button" data-command="formatBlock" data-value="blockquote">Quote</button>
        <button type="button" data-command="createLink">Link</button>
    </div>

    <div
        id="{{ $id }}_editor"
        class="rich-editor-surface rich-content"
        contenteditable="true"
        role="textbox"
        aria-multiline="true"
        aria-controls="{{ $id }}"
    >{!! $value !!}</div>

    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        maxlength="{{ $maxlength }}"
        data-rich-editor-input
    >{{ $value }}</textarea>
</div>

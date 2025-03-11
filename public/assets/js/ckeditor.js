import {
    ClassicEditor,
    AccessibilityHelp,
    Autoformat,
    Autosave,
    Alignment,
    BalloonToolbar,
    Base64UploadAdapter,
    BlockQuote,
    Bold,
    Code,
    CodeBlock,
    Essentials,
    FindAndReplace,
    FontSize,
    FontColor,
    FontBackgroundColor,
    FontFamily,
    Heading,
    Highlight,
    HorizontalLine,
    HtmlEmbed,
    Indent,
    IndentBlock,
    Italic,
    List,
    ListProperties,
    Paragraph,
    PasteFromOffice,
    SelectAll,
    SpecialCharacters,
    SpecialCharactersArrows,
    SpecialCharactersCurrency,
    SpecialCharactersEssentials,
    SpecialCharactersLatin,
    SpecialCharactersMathematical,
    SpecialCharactersText,
    Strikethrough,
    Subscript,
    Superscript,
    Table,
    TableCellProperties,
    TableProperties,
    TableToolbar,
    TextTransformation,
    TodoList,
    Underline,
    Undo
} from 'ckeditor5';

const editorConfig = {
    toolbar: {
        items: [
            'undo', 'redo', '|',
            'heading', 'fontFamily', 'fontSize', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
            'bold', 'italic', 'underline', 'blockQuote', 'subscript', 'superscript', 'specialCharacters', '|',
            'bulletedList', 'numberedList', 'todoList', 'outdent', 'indent', '|',
            'alignment', '|',
            'insertTable', 'codeBlock',
        ],
        shouldNotGroupWhenFull: false,
    },
    plugins: [
        AccessibilityHelp,
        Autoformat,
        Autosave,
        Alignment,
        BalloonToolbar,
        Base64UploadAdapter,
        BlockQuote,
        Bold,
        Code,
        CodeBlock,
        Essentials,
        FindAndReplace,
        FontSize,
        FontColor,
        FontBackgroundColor,
        FontFamily,
        Heading,
        Highlight,
        HorizontalLine,
        HtmlEmbed,
        Indent,
        IndentBlock,
        Italic,
        List,
        ListProperties,
        Paragraph,
        PasteFromOffice,
        SelectAll,
        SpecialCharacters,
        SpecialCharactersArrows,
        SpecialCharactersCurrency,
        SpecialCharactersEssentials,
        SpecialCharactersLatin,
        SpecialCharactersMathematical,
        SpecialCharactersText,
        Strikethrough,
        Subscript,
        Superscript,
        Table,
        TableCellProperties,
        TableProperties,
        TableToolbar,
        TextTransformation,
        TodoList,
        Underline,
        Undo
    ],
    balloonToolbar: [
        'bold', 'italic', 'underline', 'blockQuote', '|',
        'fontFamily', 'fontSize', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
        'bulletedList', 'numberedList', 'todoList', '|'
    ],
    heading: {
        options: [
            {
                model: 'paragraph',
                title: 'Paragraph',
                class: 'ck-heading_paragraph'
            },
            {
                model: 'heading1',
                view: 'h1',
                title: 'Heading 1',
                class: 'ck-heading_heading1'
            },
            {
                model: 'heading2',
                view: 'h2',
                title: 'Heading 2',
                class: 'ck-heading_heading2'
            },
            {
                model: 'heading3',
                view: 'h3',
                title: 'Heading 3',
                class: 'ck-heading_heading3'
            },
            {
                model: 'heading4',
                view: 'h4',
                title: 'Heading 4',
                class: 'ck-heading_heading4'
            },
            {
                model: 'heading5',
                view: 'h5',
                title: 'Heading 5',
                class: 'ck-heading_heading5'
            },
            {
                model: 'heading6',
                view: 'h6',
                title: 'Heading 6',
                class: 'ck-heading_heading6'
            }
        ]
    },
    list: {
        properties: {
            styles: true,
            startIndex: true,
            reversed: true
        }
    },
    menuBar: {
        isVisible: true
    },
    table: {
        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties'],
    },
    fontSize: {
        options: [
            9, 11, 12, 13, 'default', 17, 19, 21
        ]
    },
    fontFamily: {
        options: [
            'default',
            'Arial, Helvetica, sans-serif',
            'Courier New, Courier, monospace',
            'Georgia, serif',
            'Lucida Sans Unicode, Lucida Grande, sans-serif',
            'Tahoma, Geneva, sans-serif',
            'Times New Roman, Times, serif',
            'Trebuchet MS, Helvetica, sans-serif',
            'Verdana, Geneva, sans-serif',
            'Poppins, sans-serif',
        ]
    },
    placeholder: 'Tulis di sini!',
};

ClassicEditor.create(document.querySelector('#content'), editorConfig);
ClassicEditor.create(document.querySelector('#participant'), editorConfig);
ClassicEditor.create(document.querySelector('#decision'), editorConfig);

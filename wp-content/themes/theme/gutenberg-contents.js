const { createElement } = wp.element;
const { registerPlugin } = wp.plugins;
const { PluginDocumentSettingPanel } = wp.editPost;
const { Button } = wp.components;
const { useDispatch, useSelect } = wp.data;
const { __ } = wp.i18n;

const contentsGeneratorButton = () => {
    const { editPost } = useDispatch('core/editor');
    const content = useSelect(select => select('core/editor').getEditedPostAttribute('content'));

    const generatecontents = () => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(content, 'text/html');
        
        const h2Elements = doc.querySelectorAll('h2');
        if (h2Elements.length === 0) {
            alert(__('В статье нет заголовков H2!', 'text-domain'));
            return;
        }
        
        let contentsHtml = '<div class="post-contents"><h3 class="post-contents__title">СОДЕРЖАНИЕ СТАТЬИ</h3><ul class="post-contents__list">';
        
        h2Elements.forEach((h2, index) => {
            if (!h2.id) {
                h2.id = 'section-' + (index + 1) + '-' + h2.textContent
                    .toLowerCase()
                    .replace(/[^\wа-яё]+/gi, '-')
                    .replace(/^-+|-+$/g, '');
            }
            contentsHtml += `<li><a class="post-contents__link" href="#${h2.id}">${h2.textContent}</a></li>`;
        });
        
        contentsHtml += '</ul></div>';
        
        const h1 = doc.querySelector('h1');
        if (h1) {
            h1.insertAdjacentHTML('afterend', contentsHtml);
        } else {
            doc.body.insertAdjacentHTML('afterbegin', contentsHtml);
        }
        
        editPost({ content: doc.body.innerHTML });
    };

    return createElement(
        PluginDocumentSettingPanel,
        {
            name: 'contents-generator-panel',
            title: __('Генератор содержания', 'text-domain'),
            className: 'contents-generator-panel'
        },
        createElement(
            Button,
            {
                isPrimary: true,
                onClick: generatecontents
            },
            __('Создать содержание', 'text-domain')
        )
    );
};

registerPlugin('fermerskij-blog-contents', {
    render: contentsGeneratorButton,
    icon: 'list-view',
});
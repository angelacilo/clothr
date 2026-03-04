const React = require('react');
const e = React.createElement;

function AdminCategories() {
    const [categories, setCategories] = React.useState([]);
    const [loading, setLoading] = React.useState(true);
    const [view, setView] = React.useState('list'); // 'list' | 'create' | 'edit'
    const [selected, setSelected] = React.useState(null);
    const [form, setForm] = React.useState({ category_name: '' });
    const [msg, setMsg] = React.useState('');

    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    function loadCategories() {
        setLoading(true);
        fetch('/api/admin/categories')
            .then(function (res) { return res.json(); })
            .then(function (data) {
                setCategories(data);
                setLoading(false);
            });
    }

    React.useEffect(function () { loadCategories(); }, []);

    function openCreate() {
        setForm({ category_name: '' });
        setMsg('');
        setView('create');
    }

    function openEdit(cat) {
        setSelected(cat);
        setForm({ category_name: cat.category_name });
        setMsg('');
        setView('edit');
    }

    function handleSubmit(evt) {
        evt.preventDefault();
        var isEdit = view === 'edit';
        var url = isEdit ? '/api/admin/categories/' + selected.category_id : '/api/admin/categories';
        var method = isEdit ? 'PUT' : 'POST';
        fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify(form),
        })
            .then(function (res) { return res.json(); })
            .then(function () {
                setMsg(isEdit ? 'Category updated.' : 'Category created.');
                loadCategories();
                setView('list');
            });
    }

    function handleDelete(catId) {
        if (!confirm('Delete this category?')) return;
        fetch('/api/admin/categories/' + catId, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrf },
        })
            .then(function () { loadCategories(); });
    }

    if (loading) return e('p', { style: { padding: '2rem' } }, 'Loading categories...');

    if (view === 'create' || view === 'edit') {
        return e('div', { style: { padding: '2rem' } },
            e('button', { onClick: function () { setView('list'); } }, '← Back'),
            e('h2', null, view === 'create' ? 'New Category' : 'Edit Category'),
            msg ? e('p', { style: { color: 'green' } }, msg) : null,
            e('form', { onSubmit: handleSubmit },
                e('div', { style: formGroup },
                    e('label', null, 'Category Name'),
                    e('input', {
                        type: 'text',
                        value: form.category_name,
                        onChange: function (ev) { setForm({ category_name: ev.target.value }); },
                        required: true,
                        style: inputStyle,
                    })
                ),
                e('button', { type: 'submit', style: btnStyle }, view === 'create' ? 'Create' : 'Update')
            )
        );
    }

    return e('div', { style: { padding: '2rem' } },
        e('div', { style: { display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '1rem' } },
            e('h2', null, 'Categories'),
            e('button', { onClick: openCreate, style: btnStyle }, '+ New Category')
        ),
        msg ? e('p', { style: { color: 'green' } }, msg) : null,
        e('table', { style: { width: '100%', borderCollapse: 'collapse' } },
            e('thead', null,
                e('tr', null,
                    e('th', { style: tableTh }, 'ID'),
                    e('th', { style: tableTh }, 'Name'),
                    e('th', { style: tableTh }, 'Actions'),
                )
            ),
            e('tbody', null,
                categories.map(function (cat) {
                    return e('tr', { key: cat.category_id },
                        e('td', { style: tableTd }, cat.category_id),
                        e('td', { style: tableTd }, cat.category_name),
                        e('td', { style: tableTd },
                            e('button', { onClick: function () { openEdit(cat); }, style: { marginRight: '8px' } }, 'Edit'),
                            e('button', { onClick: function () { handleDelete(cat.category_id); }, style: { color: 'red' } }, 'Delete')
                        )
                    );
                })
            )
        )
    );
}

var tableTh = { textAlign: 'left', padding: '10px 12px', borderBottom: '2px solid #e0e0e0', fontWeight: '600' };
var tableTd = { padding: '10px 12px', borderBottom: '1px solid #eee' };
var formGroup = { marginBottom: '1rem' };
var inputStyle = { display: 'block', width: '100%', padding: '8px', marginTop: '4px', boxSizing: 'border-box' };
var btnStyle = { padding: '8px 16px', background: '#222', color: '#fff', border: 'none', borderRadius: '4px', cursor: 'pointer' };

module.exports = AdminCategories;

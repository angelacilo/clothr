const React = require('react');
const e = React.createElement;

function AdminUsers() {
    const [users, setUsers] = React.useState([]);
    const [loading, setLoading] = React.useState(true);
    const [msg, setMsg] = React.useState('');

    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    function loadUsers() {
        setLoading(true);
        fetch('/api/admin/users')
            .then(function (res) { return res.json(); })
            .then(function (data) {
                setUsers(data.data || data);
                setLoading(false);
            });
    }

    React.useEffect(function () { loadUsers(); }, []);

    function toggleRole(user) {
        var newRole = user.role === 'admin' ? 'user' : 'admin';
        fetch('/api/admin/users/' + user.id + '/role', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ role: newRole }),
        })
            .then(function (res) { return res.json(); })
            .then(function () {
                setMsg('Role updated for ' + user.name + '.');
                loadUsers();
            });
    }

    if (loading) return e('p', { style: { padding: '2rem' } }, 'Loading users...');

    return e('div', { style: { padding: '2rem' } },
        e('h2', null, 'Users'),
        msg ? e('p', { style: { color: 'green', marginBottom: '1rem' } }, msg) : null,
        e('table', { style: { width: '100%', borderCollapse: 'collapse' } },
            e('thead', null,
                e('tr', null,
                    e('th', { style: tableTh }, 'ID'),
                    e('th', { style: tableTh }, 'Name'),
                    e('th', { style: tableTh }, 'Email'),
                    e('th', { style: tableTh }, 'Role'),
                    e('th', { style: tableTh }, 'Joined'),
                    e('th', { style: tableTh }, 'Actions'),
                )
            ),
            e('tbody', null,
                users.map(function (user) {
                    return e('tr', { key: user.id },
                        e('td', { style: tableTd }, user.id),
                        e('td', { style: tableTd }, user.name),
                        e('td', { style: tableTd }, user.email),
                        e('td', { style: tableTd },
                            e('span', {
                                style: {
                                    padding: '2px 8px',
                                    borderRadius: '12px',
                                    background: user.role === 'admin' ? '#222' : '#eee',
                                    color: user.role === 'admin' ? '#fff' : '#555',
                                    fontSize: '12px',
                                }
                            }, user.role || 'user')
                        ),
                        e('td', { style: tableTd }, new Date(user.created_at).toLocaleDateString()),
                        e('td', { style: tableTd },
                            e('button', {
                                onClick: function () { toggleRole(user); },
                                style: { padding: '4px 10px', cursor: 'pointer' }
                            }, user.role === 'admin' ? 'Make User' : 'Make Admin')
                        )
                    );
                })
            )
        )
    );
}

var tableTh = { textAlign: 'left', padding: '10px 12px', borderBottom: '2px solid #e0e0e0', fontWeight: '600' };
var tableTd = { padding: '10px 12px', borderBottom: '1px solid #eee' };

module.exports = AdminUsers;

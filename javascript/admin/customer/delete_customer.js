function _0x305e() { const _0x3b2eaf = ['removeClass', 'wait', 'disabled', '#deleteModal', 'status', '823900mByMGc', '1872711sNXjDO', '104274RikqSQ', '6432130Mltdgn', '#deleteNotifyModal1', 'text', 'hidden.bs.modal', '3661590lugpeZ', 'button,\x20input', '#deleteNotifyModal2', '24ucMJWJ', '#error_message', 'json', 'show', '#errorModal', '2509191sHkJJc', '#next_button', '118753nawidj', 'modal', '#list_offset', '476zoTHfU', 'query_result', 'prop', '#prev_button', 'ready', '12kXTYMB', 'ajax', 'addClass', 'error', 'responseJSON', 'disable_link']; _0x305e = function () { return _0x3b2eaf; }; return _0x305e(); } const _0x3bc262 = _0x29ef; (function (_0x3801b4, _0x1b7fdf) { const _0x53d9cf = _0x29ef, _0xad28e1 = _0x3801b4(); while (!![]) { try { const _0x337b8b = parseInt(_0x53d9cf(0x133)) / 0x1 * (-parseInt(_0x53d9cf(0x13b)) / 0x2) + parseInt(_0x53d9cf(0x131)) / 0x3 + parseInt(_0x53d9cf(0x146)) / 0x4 + -parseInt(_0x53d9cf(0x129)) / 0x5 + parseInt(_0x53d9cf(0x148)) / 0x6 * (parseInt(_0x53d9cf(0x136)) / 0x7) + -parseInt(_0x53d9cf(0x12c)) / 0x8 * (-parseInt(_0x53d9cf(0x147)) / 0x9) + -parseInt(_0x53d9cf(0x149)) / 0xa; if (_0x337b8b === _0x1b7fdf) break; else _0xad28e1['push'](_0xad28e1['shift']()); } catch (_0xdeaa65) { _0xad28e1['push'](_0xad28e1['shift']()); } } }(_0x305e, 0xb9a0c)); let delete_id = null; function openDeleteModal(_0xcb6dc9) { const _0xb472c9 = _0x29ef; delete_id = _0xcb6dc9, $(_0xb472c9(0x144))[_0xb472c9(0x134)](_0xb472c9(0x12f)); } function _0x29ef(_0x1c23e7, _0x1c169d) { const _0x305eed = _0x305e(); return _0x29ef = function (_0x29efb6, _0x3c9e43) { _0x29efb6 = _0x29efb6 - 0x127; let _0x5b9076 = _0x305eed[_0x29efb6]; return _0x5b9076; }, _0x29ef(_0x1c23e7, _0x1c169d); } function deleteCustomer() { const _0x29dcd7 = _0x29ef, _0x448b53 = $(_0x29dcd7(0x132))['prop']('disabled'), _0x48cc8c = $('#prev_button')[_0x29dcd7(0x138)](_0x29dcd7(0x143)); $('*')[_0x29dcd7(0x13d)](_0x29dcd7(0x142)), $(_0x29dcd7(0x12a))[_0x29dcd7(0x138)](_0x29dcd7(0x143), !0x0), $('a')[_0x29dcd7(0x13d)](_0x29dcd7(0x140)), $[_0x29dcd7(0x13c)]({ 'url': '/ajax_service/admin/customer/delete_customer.php', 'type': 'DELETE', 'data': { 'id': encodeData(delete_id) }, 'headers': { 'X-CSRF-Token': CSRF_TOKEN }, 'dataType': _0x29dcd7(0x12e), 'success': function (_0x5f187c) { const _0x42710d = _0x29dcd7; $('*')[_0x42710d(0x141)]('wait'), $('button,\x20input')[_0x42710d(0x138)]('disabled', !0x1), $('a')[_0x42710d(0x141)](_0x42710d(0x140)), $(_0x42710d(0x132))[_0x42710d(0x138)](_0x42710d(0x143), _0x448b53), $('#prev_button')[_0x42710d(0x138)](_0x42710d(0x143), _0x48cc8c), $('#list_offset')[_0x42710d(0x138)]('disabled', !0x0), _0x5f187c[_0x42710d(0x13e)] ? ($(_0x42710d(0x130))['modal'](_0x42710d(0x12f)), $(_0x42710d(0x12d))[_0x42710d(0x127)](_0x5f187c['error'])) : _0x5f187c[_0x42710d(0x137)] && (0x1 === _0x5f187c[_0x42710d(0x137)] ? $(_0x42710d(0x14a))['modal'](_0x42710d(0x12f)) : 0x2 === _0x5f187c[_0x42710d(0x137)] && $(_0x42710d(0x12b))[_0x42710d(0x134)](_0x42710d(0x12f))), fetchCustomerList(); }, 'error': function (_0x473086) { const _0x23300a = _0x29dcd7; $('*')[_0x23300a(0x141)](_0x23300a(0x142)), $(_0x23300a(0x12a))[_0x23300a(0x138)](_0x23300a(0x143), !0x1), $('a')[_0x23300a(0x141)](_0x23300a(0x140)), $(_0x23300a(0x132))['prop'](_0x23300a(0x143), _0x448b53), $(_0x23300a(0x139))[_0x23300a(0x138)]('disabled', _0x48cc8c), $(_0x23300a(0x135))[_0x23300a(0x138)](_0x23300a(0x143), !0x0), _0x473086[_0x23300a(0x145)] >= 0x1f4 ? ($('#errorModal')[_0x23300a(0x134)](_0x23300a(0x12f)), $('#error_message')[_0x23300a(0x127)]('Server\x20encountered\x20error!')) : ($(_0x23300a(0x130))[_0x23300a(0x134)](_0x23300a(0x12f)), $(_0x23300a(0x12d))[_0x23300a(0x127)](_0x473086[_0x23300a(0x13f)][_0x23300a(0x13e)])); } }); } $(document)[_0x3bc262(0x13a)](function () { const _0x3efd13 = _0x3bc262; $(_0x3efd13(0x144))['on'](_0x3efd13(0x128), function () { delete_id = null; }); });
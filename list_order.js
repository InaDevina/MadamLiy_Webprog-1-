const transactions = [

  {
    id: 'TRX-002',
    date: '2026-06-06 08:31',
    items: '1x Bebek Goreng, 1x Ayam Goreng',
    cashier: 'Andi Susanto',
    payment: 'Cash',
    total: 57000,
    status: 'Completed'
  },

  {
    id: 'TRX-003',
    date: '2026-06-06 08:35',
    items: '3x Ayam Geprek',
    cashier: 'Citra Putri',
    payment: 'Transfer',
    total: 124000,
    status: 'Completed'
  },

  {
    id: 'TRX-004',
    date: '2026-06-06 08:38',
    items: '2x Es Teh Manis',
    cashier: 'Citra Putri',
    payment: 'QRIS',
    total: 51000,
    status: 'Process'
  }

];

function renderTable() {

  const tbody =
    document.getElementById('transactionTable');

  tbody.innerHTML = '';

  transactions.forEach((trx, index) => {

    const paymentClass = {

      Cash: 'badge-cash',
      Transfer: 'badge-transfer',
      QRIS: 'badge-qris'

    }[trx.payment];

    const statusClass =

      trx.status === 'Selesai'
        ? 'badge-completed'
        : 'badge-process';

    tbody.innerHTML += `

        <tr>

            <td><strong>${trx.id}</strong></td>

            <td>${trx.date}</td>

            <td>${trx.items}</td>

            <td>${trx.cashier}</td>

            <td>

                <span class="badge ${paymentClass}">
                    ${trx.payment}
                </span>

            </td>

            <td class="total-price">
                Rp ${trx.total.toLocaleString('id-ID')}
            </td>

            <td>

                <span class="badge ${statusClass}">
                    ${trx.status}
                </span>

            </td>

            <td>

                <div class="action-icons">

                    <i
                    class="fa-regular fa-pen-to-square edit-btn"
                    onclick="toggleStatus(${index})">
                    </i>

                    <i
                    class="fa-regular fa-trash-can delete-btn"
                    onclick="deleteOrder(${index})">
                    </i>

                </div>

            </td>

        </tr>

        `;
  });
}

function completeOrder(index) {

  transactions[index].status =
    'Completed';

  renderTable();

}

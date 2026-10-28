<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Facture {{ $order->order_number }} — Morada Lodge</title>
    <style>
        :root { --brown: #8b4513; --line: #e6d8c8; --ink: #2c1810; --muted: #6b5548; }

        * { box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            color: var(--ink);
            background: #f4efe8;
            margin: 0;
            padding: 26px 14px 60px;
        }

        .sheet {
            max-width: 780px;
            margin: 0 auto;
            background: #fff;
            padding: 34px 38px 40px;
            border-radius: 10px;
            box-shadow: 0 6px 24px rgba(0,0,0,.08);
        }

        .head { display: flex; justify-content: space-between; gap: 24px; flex-wrap: wrap;
                border-bottom: 3px solid var(--brown); padding-bottom: 18px; margin-bottom: 24px; }
        .brand-name { font-size: 1.45rem; font-weight: 700; color: var(--brown); margin: 0; }
        .brand-sub { margin: 4px 0 0; font-size: .84rem; color: var(--muted); }

        .doc-title { text-align: right; }
        .doc-title h2 { margin: 0; font-size: 1.15rem; letter-spacing: .06em; text-transform: uppercase; color: var(--brown); }
        .doc-title .num { font-size: 1.1rem; font-weight: 700; margin-top: 4px; }
        .doc-title .date { font-size: .82rem; color: var(--muted); margin-top: 3px; }

        .meta { display: flex; gap: 30px; flex-wrap: wrap; margin-bottom: 24px; }
        .meta-block { flex: 1 1 210px; }
        .meta-block h3 { font-size: .74rem; text-transform: uppercase; letter-spacing: .06em;
                         color: var(--muted); margin: 0 0 7px; }
        .meta-block p { margin: 0; font-size: .9rem; line-height: 1.55; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { text-align: left; font-size: .74rem; text-transform: uppercase; letter-spacing: .05em;
             color: var(--muted); padding: 9px 10px; border-bottom: 2px solid var(--line); }
        td { padding: 11px 10px; border-bottom: 1px solid var(--line); font-size: .9rem; }
        .right { text-align: right; }
        .center { text-align: center; }

        .total-row td { border: none; padding-top: 14px; font-size: 1.05rem; font-weight: 700; }
        .total-row .amount { color: var(--brown); font-size: 1.25rem; }

        .status {
            display: inline-block; padding: 4px 12px; border-radius: 999px;
            font-size: .76rem; font-weight: 700; background: #f1ece6; color: var(--muted);
        }
        .status.paid { background: #dcfce7; color: #15803d; }
        .status.cancelled { background: #fee2e2; color: #b91c1c; }

        .notes { background: #fbf7f2; border-left: 3px solid var(--brown);
                 padding: 11px 14px; font-size: .86rem; margin-bottom: 22px; }

        .foot { border-top: 1px solid var(--line); padding-top: 16px; text-align: center;
                font-size: .8rem; color: var(--muted); }

        .actions { max-width: 780px; margin: 18px auto 0; text-align: center; }
        .btn {
            display: inline-block; padding: 10px 20px; margin: 0 5px; border-radius: 8px;
            border: 1px solid var(--line); background: #fff; color: var(--brown);
            text-decoration: none; font-size: .87rem; font-weight: 600; cursor: pointer;
        }
        .btn-primary { background: var(--brown); color: #fff; border-color: var(--brown); }

        @media print {
            body { background: #fff; padding: 0; }
            .sheet { box-shadow: none; border-radius: 0; max-width: none; padding: 0; }
            .actions { display: none; }
        }
    </style>
</head>
<body>
    @php
        $paymentLabels = [
            'cash' => 'Espèces',
            'card' => 'Carte bancaire',
            'mobile' => 'Mobile money',
            'room_charge' => 'Sur la note de la chambre',
            'online' => 'En ligne',
        ];
    @endphp

    <div class="sheet">
        <div class="head">
            <div>
                <h1 class="brand-name">Morada Lodge</h1>
                <p class="brand-sub">Restaurant de l'hôtel</p>
            </div>
            <div class="doc-title">
                <h2>Facture</h2>
                <div class="num">{{ $order->order_number }}</div>
                <div class="date">{{ $order->created_at->format('d/m/Y à H:i') }}</div>
            </div>
        </div>

        <div class="meta">
            <div class="meta-block">
                <h3>Client</h3>
                <p>
                    <strong>{{ $order->client_name }}</strong>
                    @if($order->customer && $order->customer->phone)
                        <br>{{ $order->customer->phone }}
                    @endif
                </p>
            </div>
            <div class="meta-block">
                <h3>Service</h3>
                <p>
                    {{ $order->type_label }}<br>
                    {{ $order->destination }}
                </p>
            </div>
            <div class="meta-block">
                <h3>Statut</h3>
                <p>
                    <span class="status {{ $order->status === 'paid' ? 'paid' : ($order->status === 'cancelled' ? 'cancelled' : '') }}">
                        {{ $order->status_label }}
                    </span>
                    @if($order->payment_method)
                        <br>{{ $paymentLabels[$order->payment_method] ?? $order->payment_method }}
                    @endif
                    @if($order->paid_at)
                        <br>Payée le {{ $order->paid_at->format('d/m/Y H:i') }}
                    @endif
                </p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Désignation</th>
                    <th class="center">P.U.</th>
                    <th class="center">Qté</th>
                    <th class="right">Montant</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->menu->name ?? 'Plat retiré de la carte' }}</td>
                        <td class="center">{{ Helper::formatCFA($item->price) }}</td>
                        <td class="center">{{ $item->quantity }}</td>
                        <td class="right">{{ Helper::formatCFA($item->price * $item->quantity) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" class="right">Total à payer</td>
                    <td class="right amount">{{ $order->formatted_total }}</td>
                </tr>
            </tbody>
        </table>

        @if($order->notes)
            <div class="notes"><strong>Note :</strong> {{ $order->notes }}</div>
        @endif

        <div class="foot">
            Merci de votre visite — nous espérons vous revoir bientôt.
            @if($order->createdBy)
                <br>Servi par {{ $order->createdBy->name }}
            @endif
        </div>
    </div>

    <div class="actions">
        <button class="btn btn-primary" onclick="window.print()">Imprimer</button>
        <a class="btn" href="{{ route('restaurant.orders.show', $order->id) }}">Retour à la commande</a>
    </div>
</body>
</html>

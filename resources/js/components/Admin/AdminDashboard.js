import React, { useEffect, useState } from "react";


// ── tiny SVG chart helpers ────────────────────────────────────────────────────

function Sparkline({ data = [], color = "#f97316", height = 48 }) {
    if (!data.length) return null;
    const max = Math.max(...data, 1);
    const min = Math.min(...data);
    const w = 200, h = height;
    const pts = data.map((v, i) => {
        const x = (i / (data.length - 1)) * w;
        const y = h - ((v - min) / (max - min || 1)) * h;
        return `${x},${y}`;
    }).join(" ");
    return (
        <svg viewBox={`0 0 ${w} ${h}`} style={{ width: "100%", height }} preserveAspectRatio="none">
            <polyline points={pts} fill="none" stroke={color} strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" />
        </svg>
    );
}

function DonutChart({ segments = [], size = 110 }) {
    const total = segments.reduce((s, x) => s + (x.value || 0), 0) || 1;
    let cum = 0;
    const r = 40, cx = size / 2, cy = size / 2;
    const arc = (start, end) => {
        const a1 = (start / total) * 2 * Math.PI - Math.PI / 2;
        const a2 = (end / total) * 2 * Math.PI - Math.PI / 2;
        const x1 = cx + r * Math.cos(a1), y1 = cy + r * Math.sin(a1);
        const x2 = cx + r * Math.cos(a2), y2 = cy + r * Math.sin(a2);
        const large = end - start > total / 2 ? 1 : 0;
        return `M ${cx} ${cy} L ${x1} ${y1} A ${r} ${r} 0 ${large} 1 ${x2} ${y2} Z`;
    };
    return (
        <svg viewBox={`0 0 ${size} ${size}`} width={size} height={size}>
            {segments.map((seg, i) => {
                const path = arc(cum, cum + seg.value);
                cum += seg.value;
                return <path key={i} d={path} fill={seg.color} opacity="0.9" />;
            })}
            <circle cx={cx} cy={cy} r={r * 0.58} fill="#0f1117" />
        </svg>
    );
}

function BarChart({ labels = [], data = [], color = "#f97316" }) {
    const max = Math.max(...data, 1);
    return (
        <div style={{ display: "flex", alignItems: "flex-end", gap: 6, height: 80, width: "100%" }}>
            {data.map((v, i) => (
                <div key={i} style={{ flex: 1, display: "flex", flexDirection: "column", alignItems: "center", gap: 4 }}>
                    <div style={{
                        width: "100%", background: color,
                        height: `${(v / max) * 68}px`,
                        borderRadius: "3px 3px 0 0",
                        opacity: 0.85,
                        transition: "height 0.6s ease"
                    }} />
                    <span style={{ fontSize: 9, color: "#6b7280", whiteSpace: "nowrap", overflow: "hidden", maxWidth: "100%", textOverflow: "ellipsis" }}>
                        {labels[i] ? String(labels[i]).slice(0, 6) : ""}
                    </span>
                </div>
            ))}
        </div>
    );
}

// ── styles ────────────────────────────────────────────────────────────────────
const css = `
  @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap');

  .clothr-dash * { box-sizing: border-box; margin: 0; padding: 0; }
  .clothr-dash {
    font-family: 'Syne', sans-serif;
    background: #0b0d12;
    color: #e8e8e8;
    min-height: 100vh;
    padding: 28px 32px;
  }

  .dash-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 32px;
  }
  .dash-logo { font-size: 22px; font-weight: 800; letter-spacing: -0.5px; color: #fff; }
  .dash-logo span { color: #f97316; }
  .dash-subtitle { font-size: 12px; color: #6b7280; font-family: 'JetBrains Mono', monospace; margin-top: 2px; }
  .dash-badge {
    background: #f9731618; border: 1px solid #f9731640;
    color: #f97316; font-size: 11px; font-family: 'JetBrains Mono', monospace;
    padding: 4px 12px; border-radius: 20px;
  }

  .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 20px; }
  .kpi-card {
    background: #13161e; border: 1px solid #1e2130;
    border-radius: 12px; padding: 20px;
    transition: border-color 0.2s;
  }
  .kpi-card:hover { border-color: #f9731640; }
  .kpi-label { font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
  .kpi-value { font-size: 28px; font-weight: 800; color: #fff; letter-spacing: -1px; }
  .kpi-sub { font-size: 11px; color: #6b7280; margin-top: 4px; font-family: 'JetBrains Mono', monospace; }
  .kpi-accent { color: #f97316; }

  .mid-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 16px; margin-bottom: 20px; }
  .card {
    background: #13161e; border: 1px solid #1e2130;
    border-radius: 12px; padding: 20px;
  }
  .card-title { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-bottom: 16px; font-weight: 600; }

  .legend { display: flex; flex-direction: column; gap: 8px; margin-top: 12px; }
  .legend-row { display: flex; align-items: center; justify-content: space-between; font-size: 12px; }
  .legend-dot { width: 8px; height: 8px; border-radius: 50%; margin-right: 8px; flex-shrink: 0; }
  .legend-label { display: flex; align-items: center; color: #9ca3af; }
  .legend-val { color: #e8e8e8; font-family: 'JetBrains Mono', monospace; font-size: 11px; }

  .bot-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

  .prod-list { display: flex; flex-direction: column; gap: 10px; }
  .prod-row { display: flex; align-items: center; gap: 12px; }
  .prod-rank { font-size: 10px; font-family: 'JetBrains Mono', monospace; color: #4b5563; width: 18px; }
  .prod-bar-wrap { flex: 1; background: #1e2130; border-radius: 4px; height: 6px; overflow: hidden; }
  .prod-bar { height: 100%; background: #f97316; border-radius: 4px; transition: width 0.8s ease; }
  .prod-name { font-size: 12px; color: #d1d5db; flex: 0 0 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .prod-count { font-size: 11px; font-family: 'JetBrains Mono', monospace; color: #f97316; width: 30px; text-align: right; }

  .alert-row { display: flex; gap: 12px; margin-bottom: 20px; }
  .alert-card {
    flex: 1; background: #13161e; border-radius: 12px; padding: 14px 18px;
    display: flex; align-items: center; gap: 12px;
    border: 1px solid #1e2130;
  }
  .alert-icon { font-size: 20px; }
  .alert-text { font-size: 12px; color: #9ca3af; }
  .alert-text strong { color: #e8e8e8; display: block; font-size: 14px; margin-bottom: 2px; }
  .alert-warn { border-color: #f9731640 !important; }
  .alert-ok   { border-color: #10b98140 !important; }

  .loading {
    display: flex; align-items: center; justify-content: center;
    height: 60vh; font-size: 13px; color: #6b7280;
    font-family: 'JetBrains Mono', monospace;
    flex-direction: column; gap: 12px;
  }
  .spinner {
    width: 32px; height: 32px; border: 2px solid #1e2130;
    border-top-color: #f97316; border-radius: 50%;
    animation: spin 0.7s linear infinite;
  }
  @keyframes spin { to { transform: rotate(360deg); } }

  @media (max-width: 1100px) {
    .kpi-grid { grid-template-columns: repeat(2, 1fr); }
    .mid-grid { grid-template-columns: 1fr 1fr; }
    .bot-grid { grid-template-columns: 1fr; }
  }
  @media (max-width: 640px) {
    .clothr-dash { padding: 16px; }
    .kpi-grid { grid-template-columns: 1fr 1fr; }
    .mid-grid { grid-template-columns: 1fr; }
    .alert-row { flex-direction: column; }
  }
`;

// ── main component ────────────────────────────────────────────────────────────
function AdminDashboard() {
    const [stats, setStats] = useState(null);
    const [error, setError] = useState(null);

    useEffect(() => {
        fetch("/api/admin/stats")
            .then(r => r.json())
            .then(setStats)
            .catch(() => setError("Failed to load dashboard data. Make sure /admin/api/stats is accessible."));
    }, []);

    const fmt = v => "$" + parseFloat(v || 0).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const num = v => parseInt(v || 0).toLocaleString();

    const STATUS_COLORS = {
        pending:    "#f59e0b",
        processing: "#3b82f6",
        shipped:    "#8b5cf6",
        delivered:  "#10b981",
        cancelled:  "#ef4444",
    };

    return (
        <>
            <style>{css}</style>
            <div className="clothr-dash">
                {/* Header */}
                <div className="dash-header">
                    <div>
                        <div className="dash-logo">CLOTH<span>R</span></div>
                        <div className="dash-subtitle">admin dashboard · live data</div>
                    </div>
                    <div className="dash-badge">● LIVE</div>
                </div>

                {!stats && !error && (
                    <div className="loading">
                        <div className="spinner" />
                        fetching dashboard data…
                    </div>
                )}
                {error && (
                    <div className="loading" style={{ color: "#ef4444" }}>{error}</div>
                )}

                {stats && (() => {
                    const os = stats.orderStatus || {};
                    const donutSegments = Object.entries(STATUS_COLORS).map(([k, color]) => ({
                        label: k, value: os[k] || 0, color
                    }));
                    const topLabels = stats.topProducts?.labels || [];
                    const topData   = stats.topProducts?.data   || [];
                    const maxTop    = Math.max(...topData, 1);

                    return (
                        <>
                            {/* Alert strip */}
                            <div className="alert-row">
                                <div className={`alert-card ${stats.lowStockCount > 0 ? "alert-warn" : "alert-ok"}`}>
                                    <span className="alert-icon">{stats.lowStockCount > 0 ? "⚠️" : "✅"}</span>
                                    <div className="alert-text">
                                        <strong>{stats.lowStockCount} items low stock</strong>
                                        {stats.lowStockCount > 0 ? "Restock needed soon" : "Inventory looks healthy"}
                                    </div>
                                </div>
                                <div className="alert-card">
                                    <span className="alert-icon">⭐</span>
                                    <div className="alert-text">
                                        <strong>{stats.avgRating} avg rating</strong>
                                        across all reviews
                                    </div>
                                </div>
                                <div className="alert-card">
                                    <span className="alert-icon">📦</span>
                                    <div className="alert-text">
                                        <strong>{num(stats.totalProducts)} products</strong>
                                        {num(stats.totalOrdersCount)} total orders
                                    </div>
                                </div>
                                <div className="alert-card">
                                    <span className="alert-icon">👥</span>
                                    <div className="alert-text">
                                        <strong>{num(stats.activeUsers)} customers</strong>
                                        registered accounts
                                    </div>
                                </div>
                            </div>

                            {/* KPIs */}
                            <div className="kpi-grid">
                                <div className="kpi-card">
                                    <div className="kpi-label">Total Revenue</div>
                                    <div className="kpi-value kpi-accent">{fmt(stats.totalRevenue)}</div>
                                    <div className="kpi-sub">completed orders only</div>
                                </div>
                                <div className="kpi-card">
                                    <div className="kpi-label">Today's Orders</div>
                                    <div className="kpi-value">{num(stats.todaysOrders)}</div>
                                    <div className="kpi-sub">placed today</div>
                                </div>
                                <div className="kpi-card">
                                    <div className="kpi-label">Avg Order Value</div>
                                    <div className="kpi-value">{fmt(stats.avgOrderValue)}</div>
                                    <div className="kpi-sub">per completed order</div>
                                </div>
                                <div className="kpi-card">
                                    <div className="kpi-label">Total Customers</div>
                                    <div className="kpi-value">{num(stats.totalCustomers)}</div>
                                    <div className="kpi-sub">registered users</div>
                                </div>
                            </div>

                            {/* Mid row */}
                            <div className="mid-grid">
                                <div className="card">
                                    <div className="card-title">Sales Trend — last 8 days</div>
                                    <Sparkline data={stats.salesTrend?.data?.map(Number) || []} color="#f97316" height={72} />
                                    <div style={{ display: "flex", justifyContent: "space-between", marginTop: 8 }}>
                                        {(stats.salesTrend?.labels || []).map((l, i) => (
                                            <span key={i} style={{ fontSize: 9, color: "#4b5563", fontFamily: "JetBrains Mono, monospace" }}>
                                                {String(l).slice(5)}
                                            </span>
                                        ))}
                                    </div>
                                </div>

                                <div className="card" style={{ display: "flex", flexDirection: "column", alignItems: "center" }}>
                                    <div className="card-title" style={{ alignSelf: "flex-start" }}>Order Status</div>
                                    <DonutChart segments={donutSegments} size={110} />
                                    <div className="legend" style={{ width: "100%", marginTop: 8 }}>
                                        {donutSegments.map(s => (
                                            <div className="legend-row" key={s.label}>
                                                <div className="legend-label">
                                                    <div className="legend-dot" style={{ background: s.color }} />
                                                    {s.label}
                                                </div>
                                                <span className="legend-val">{s.value}</span>
                                            </div>
                                        ))}
                                    </div>
                                </div>

                                <div className="card">
                                    <div className="card-title">Revenue by Category</div>
                                    <BarChart
                                        labels={stats.categoryRevenue?.labels || []}
                                        data={(stats.categoryRevenue?.data || []).map(Number)}
                                        color="#f97316"
                                    />
                                </div>
                            </div>

                            {/* Bottom row */}
                            <div className="bot-grid">
                                <div className="card">
                                    <div className="card-title">Top Selling Products</div>
                                    <div className="prod-list">
                                        {topLabels.map((name, i) => (
                                            <div className="prod-row" key={i}>
                                                <span className="prod-rank">#{i + 1}</span>
                                                <span className="prod-name">{name}</span>
                                                <div className="prod-bar-wrap">
                                                    <div className="prod-bar" style={{ width: `${(topData[i] / maxTop) * 100}%` }} />
                                                </div>
                                                <span className="prod-count">{topData[i]}</span>
                                            </div>
                                        ))}
                                        {!topLabels.length && <div style={{ color: "#4b5563", fontSize: 12 }}>No data yet</div>}
                                    </div>
                                </div>

                                <div className="card">
                                    <div className="card-title">Order Breakdown</div>
                                    <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 14 }}>
                                        {[
                                            { label: "Pending",    val: num(os.pending),    color: STATUS_COLORS.pending },
                                            { label: "Processing", val: num(os.processing), color: STATUS_COLORS.processing },
                                            { label: "Shipped",    val: num(os.shipped),    color: STATUS_COLORS.shipped },
                                            { label: "Delivered",  val: num(os.delivered),  color: STATUS_COLORS.delivered },
                                            { label: "Cancelled",  val: num(os.cancelled),  color: STATUS_COLORS.cancelled },
                                            { label: "All Orders", val: num(stats.totalOrdersCount), color: "#e8e8e8" },
                                        ].map(s => (
                                            <div key={s.label} style={{ background: "#0f1117", borderRadius: 8, padding: "12px 14px" }}>
                                                <div style={{ fontSize: 10, color: "#6b7280", textTransform: "uppercase", letterSpacing: "0.8px", marginBottom: 4 }}>{s.label}</div>
                                                <div style={{ fontSize: 22, fontWeight: 800, color: s.color, letterSpacing: -1 }}>{s.val}</div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </>
                    );
                })()}
            </div>
        </>
    );
}

export default AdminDashboard;
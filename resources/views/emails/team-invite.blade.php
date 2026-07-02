<!DOCTYPE html>
<html dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #0a0a0f; margin: 0; padding: 0; color: #e0e0e5; }
        .container { max-width: 560px; margin: 40px auto; background: #12121a; border-radius: 16px; overflow: hidden; border: 1px solid #1e1e2e; }
        .header { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); padding: 36px 24px; text-align: center; position: relative; overflow: hidden; }
        .header::before { content: ''; position: absolute; inset: 0; background: radial-gradient(circle at 50% 120%, rgba(68,136,255,0.08), transparent 60%); }
        .header h1 { margin: 0 0 6px; font-size: 22px; font-weight: 900; color: #fff; position: relative; }
        .header p { margin: 0; font-size: 13px; color: #6b7280; font-family: 'Courier New', monospace; position: relative; }
        .content { padding: 32px 28px; }
        .invite-box { background: linear-gradient(135deg, #1e1e2e, #161625); border: 1px solid #2a2a3e; border-radius: 12px; padding: 24px; margin: 24px 0; text-align: center; }
        .invite-box .label { font-size: 10px; text-transform: uppercase; letter-spacing: 3px; color: #4488ff; margin-bottom: 16px; font-family: 'Courier New', monospace; }
        .invite-box .inviter { font-size: 15px; font-weight: 700; color: #e0e0e5; margin-bottom: 4px; }
        .invite-box .team-name { font-size: 24px; font-weight: 900; color: #4488ff; margin: 8px 0; }
        .invite-box .team-type { display: inline-block; font-size: 11px; font-family: 'Courier New', monospace; padding: 4px 12px; border-radius: 20px; margin-top: 8px; }
        .type-learning { background: rgba(68,136,255,0.1); color: #4488ff; border: 1px solid rgba(68,136,255,0.2); }
        .type-project { background: rgba(255,170,51,0.1); color: #ffaa33; border: 1px solid rgba(255,170,51,0.2); }
        .code-section { text-align: center; margin: 28px 0; }
        .code-label { font-size: 11px; color: #6b7280; margin-bottom: 12px; font-family: 'Courier New', monospace; text-transform: uppercase; letter-spacing: 2px; }
        .code-chars { display: inline-flex; gap: 6px; }
        .code-char { width: 44px; height: 52px; background: #1a1a2e; border: 1px solid #2a2a3e; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 900; font-family: 'Courier New', monospace; color: #fff; }
        .message { font-size: 14px; line-height: 1.7; color: #9ca3af; margin: 16px 0; }
        .btn { display: inline-block; background: linear-gradient(135deg, #4488ff, #3366dd); color: #fff !important; padding: 14px 36px; text-decoration: none; border-radius: 10px; font-weight: 800; font-size: 14px; margin: 20px 0; }
        .steps { background: #161620; border-radius: 10px; padding: 20px; margin: 20px 0; }
        .step { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px; }
        .step:last-child { margin-bottom: 0; }
        .step-num { width: 24px; height: 24px; background: #1e1e2e; border: 1px solid #2a2a3e; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; color: #4488ff; font-family: 'Courier New', monospace; flex-shrink: 0; }
        .step-text { font-size: 13px; color: #9ca3af; padding-top: 2px; }
        .footer { background: #0e0e16; padding: 20px; text-align: center; font-size: 12px; color: #4b5563; border-top: 1px solid #1e1e2e; }
        .footer a { color: #4488ff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎓 LearningGuided</h1>
            <p>team.invite()</p>
        </div>

        <div class="content">
            <div class="invite-box">
                <div class="label">Team Invitation</div>
                <div class="inviter">{{ $inviterName }} invited you to join</div>
                <div class="team-name">{{ $teamName }}</div>
                <span class="team-type {{ $teamType === 'learning' ? 'type-learning' : 'type-project' }}">
                    {{ $teamType === 'learning' ? '📚 Learning Team' : '🚀 Project Team' }}
                </span>
            </div>

            <div class="code-section">
                <div class="code-label">Join Code</div>
                <div class="code-chars">
                    @foreach(str_split($teamCode) as $char)
                        <span class="code-char">{{ $char }}</span>
                    @endforeach
                </div>
            </div>

            <p class="message">
                {{ $inviterName }} wants you to join their team on LearningGuided.
                Use the code above to join, or follow these steps:
            </p>

            <div class="steps">
                <div class="step">
                    <span class="step-num">1</span>
                    <span class="step-text">Log in to LearningGuided</span>
                </div>
                <div class="step">
                    <span class="step-num">2</span>
                    <span class="step-text">Go to <strong>Teams</strong> → <strong>Join with Code</strong></span>
                </div>
                <div class="step">
                    <span class="step-num">3</span>
                    <span class="step-text">Enter the code: <strong style="color:#4488ff; letter-spacing:2px;">{{ $teamCode }}</strong></span>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} LearningGuided</p>
            <p>You received this because someone invited you. You can ignore it if unexpected.</p>
        </div>
    </div>
</body>
</html>

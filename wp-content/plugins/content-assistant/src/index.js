import { registerPlugin } from '@wordpress/plugins';
import { PluginSidebar } from '@wordpress/editor'; // Ensures compatibility
import { TextControl, TextareaControl, Button } from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';
import { dispatch, select } from '@wordpress/data';

const ContentAssistantSidebar = () => {
	const [urls, setUrls] = useState([""]);
	const [prompt, setPrompt] = useState("");
	const [generatedCopy, setGeneratedCopy] = useState("");

	useEffect(() => {
		// Ensure sidebar DOM elements are ready before executing anything
		const sidebar = document.querySelector('.edit-post-sidebar');
		if (!sidebar) {
			console.error("Sidebar DOM element not found.");
		}
	}, []);

	const addUrlInput = () => {
		setUrls([...urls, ""]);
	};

	const updateUrl = (index, value) => {
		const newUrls = [...urls];
		newUrls[index] = value;
		setUrls(newUrls);
	};

	const removeUrlInput = (index) => {
		const newUrls = urls.filter((_, i) => i !== index);
		setUrls(newUrls.length > 0 ? newUrls : [""]); // Ensure at least one input remains
	};

	const generateCopy = async () => {
		console.log("Sending AJAX request to PHP...");

		try {
			const response = await wp.apiRequest({
				path: '/content-assistant/v1/generate',
				method: 'POST',
				data: JSON.stringify({ urls, prompt }),
				headers: {
					'Content-Type': 'application/json',
				},
			});

			console.log("Server Response:", response);

			setGeneratedCopy(response.generated_copy || "No content received.");
		} catch (error) {
			console.error("AJAX Error:", error);
			setGeneratedCopy("Error generating content. Check the console.");
		}
	};



	const insertCopyIntoBlock = () => {
		const selectedBlock = select('core/block-editor').getSelectedBlockClientId();
		if (selectedBlock) {
			dispatch('core/block-editor').updateBlockAttributes(selectedBlock, { content: generatedCopy });
			setGeneratedCopy("");
		}
	};

	return (
		<PluginSidebar
			name="content-assistant-sidebar"
			title="Content Assistant"
		>
			<div style={{ padding: '10px' }}>
				<h3>URLs to crawl</h3>
				{urls.map((url, index) => (
					<div key={index} style={{ display: 'flex', alignItems: 'center', marginBottom: '8px' }}>
						<TextControl
							value={url}
							onChange={(value) => updateUrl(index, value)}
							placeholder="Enter a URL"
							__nextHasNoMarginBottom={true}
							style={{ flexGrow: 1, marginRight: '8px' }}
						/>
						<Button isDestructive onClick={() => removeUrlInput(index)}>-</Button>
					</div>
				))}
				<Button isSecondary onClick={addUrlInput}>+ Add URL</Button>

				<h3 style={{ marginTop: '15px' }}>Prompt</h3>
				<TextareaControl
					value={prompt}
					onChange={(value) => setPrompt(value)}
					placeholder="Enter your prompt here"
					__nextHasNoMarginBottom={true}
				/>

				<Button isPrimary onClick={generateCopy} style={{ marginTop: '15px' }}>Generate Copy</Button>

				<h3 style={{ marginTop: '15px' }}>Generated Copy</h3>
				<TextareaControl
					value={generatedCopy}
					readOnly
					placeholder="Generated copy will appear here"
					__nextHasNoMarginBottom={true}
				/>

				<Button isSecondary onClick={insertCopyIntoBlock} disabled={!generatedCopy} style={{ marginTop: '10px' }}>
					Insert Copy
				</Button>
			</div>
		</PluginSidebar>
	);
};

registerPlugin('content-assistant-sidebar', { render: ContentAssistantSidebar });

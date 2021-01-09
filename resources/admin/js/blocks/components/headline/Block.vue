<template>
    <div class="editor">
        <editor-menu-bubble :editor="editor" :keep-in-bounds="keepInBounds" v-slot="{ commands, isActive, menu }">
            <div
                class="menububble"
                :class="{ 'is-active': menu.isActive }"
                :style="`left: ${menu.left}px; bottom: ${menu.bottom}px;`"
            >

                <button
                class="menububble__button"
                :class="{ 'is-active': isActive.bold() }"
                @click="commands.bold"
                >
                <icon name="bold" />
                </button>

                <button
                class="menububble__button"
                :class="{ 'is-active': isActive.italic() }"
                @click="commands.italic"
                >
                <icon name="italic" />
                </button>

                <button
                class="menububble__button"
                :class="{ 'is-active': isActive.code() }"
                @click="commands.code"
                >
                <icon name="code" />
                </button>

            </div>
        </editor-menu-bubble>

        <component :is='settings.headlineTag'>
            <editor-content v-bind:style="headlineStyles" :class="settings.customClass" :editor="editor"/>
        </component>
    </div>
</template>

<script>
    import GeneralMixin from '../../mixins/GeneralMixin'
    import { mapGetters, mapActions } from 'vuex'
    import Icon from '../../../components/ui/Icon'
    import { Editor, EditorContent, EditorMenuBar, EditorMenuBubble } from 'tiptap'
    import {
        Blockquote,
        CodeBlock,
        HardBreak,
        Heading,
        HorizontalRule,
        OrderedList,
        BulletList,
        ListItem,
        TodoItem,
        TodoList,
        Bold,
        Code,
        Italic,
        Link,
        Strike,
        Underline,
        History,
    } from 'tiptap-extensions'

    export default {
        components: {
            Icon,
            EditorContent,
            EditorMenuBar,
            EditorMenuBubble,
        },
        mixins: [GeneralMixin],
        data() {
            return {
                editor: null,
                keepInBounds: true,
                blockContent: this.$store.getters[`${this.storePath}/itemContent`](this.uniqueId) ? this.$store.getters[`${this.storePath}/itemContent`](this.uniqueId) : '',
            }
        },
        computed: {
            blockType: {
                get() {
                    return this.$store.getters[`${this.storePath}/itemType`](this.uniqueId)
                }
            },
            headlineStyles() {
                let styleObj = {
                    fontFamily: this.settings.fontFamily,
                    fontSize: (this.settings.headlineTag == 'div') ? this.settings.fontSize : null,
                    color: this.settings.textColor,
                    fontWeight: (this.settings.fontWeight != 'default') ? this.settings.fontWeight : null,
                    padding: this.settings.padding,
                    textShadow: this.settings.textShadow,
                    backgroundColor: this.settings.backgroundColor
                }

                return styleObj
            },
            settings() {
                return this.$store.getters[`${this.storePath}/itemSettings`](this.uniqueId)
            },
            content() {
                return this.$store.getters[`${this.storePath}/itemContent`](this.uniqueId)
            },
        },
        watch: {
            blockContent: {
                handler(val) {
                    this.updateItemContent({content: val, uniqueId: this.uniqueId})
                },
                deep: true
            }
        },
        methods: {
        },
        mounted() {
            this.editor = new Editor({
                extensions: [
                    new Blockquote(),
                    new BulletList(),
                    new CodeBlock(),
                    new HardBreak(),
                    new Heading({ levels: [1, 2, 3] }),
                    new HorizontalRule(),
                    new ListItem(),
                    new OrderedList(),
                    new TodoItem(),
                    new TodoList(),
                    new Link(),
                    new Bold(),
                    new Code(),
                    new Italic(),
                    new Strike(),
                    new Underline(),
                    new History(),
                ],
                content: this.blockContent,
                onUpdate: ({ getHTML }) => {
                    this.blockContent = getHTML()
                },
            })
            // this.$refs['headline-editor-' + this.uniqueId].api.elements[0].focus()
        },
        beforeDestroy() {
            this.editor.destroy()
        },
    }
</script>

<style lang="scss" scoped>

    .menububble {
        position: absolute;
        display: -webkit-box;
        display: flex;
        z-index: 20;
        background: #000;
        border-radius: 5px;
        padding: .3rem;
        margin-bottom: .5rem;
        -webkit-transform: translateX(-50%);
        transform: translateX(-50%);
        visibility: hidden;
        opacity: 0;
        -webkit-transition: opacity .2s,visibility .2s;
        transition: opacity .2s,visibility .2s;
    }

    .menububble__button {
        display: -webkit-inline-box;
        display: inline-flex;
        background: transparent;
        border: 0;
        color: #fff;
        padding: .2rem .5rem;
        margin-right: .2rem;
        border-radius: 3px;
        cursor: pointer;
    }

    .menububble.is-active {
        opacity: 1;
        visibility: visible;
    }
</style>
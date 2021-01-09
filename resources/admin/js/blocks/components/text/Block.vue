<template>
    <div :style="textStyles">
        text editor
    </div>
</template>

<script>
    import GeneralMixin from '../../mixins/GeneralMixin'
    import { mapGetters, mapActions } from 'vuex'

    export default {
        components: { },
        mixins: [GeneralMixin],
        customSettings: {
            blockTitle: {type: String, default: 'Text'},
            textClass: {type: String, default: ''},
            showEditorToolbar: {type: Boolean, default: false},
        },
        data() {
            return {
                blockContent: this.$store.getters[`${this.storePath}/itemContent`](this.uniqueId) || ''
            }
        },
        computed: {
            textStyles() {
                let styleObj = {
                    color: this.settings.textColor,
                    fontSize: this.settings.fontSize,
                    lineHeight: this.settings.fontLineHeight,
                    padding: this.settings.padding,
                    textShadow: this.settings.textShadow,
                    backgroundColor: this.settings.backgroundColor
                }

                return styleObj
            },
            blockType: {
                get() {
                    return this.$store.getters[`${this.storePath}/itemType`](this.uniqueId)
                }
            },
            settings() {
                return this.$store.getters[`${this.storePath}/itemSettings`](this.uniqueId)
            },
            content() {
                return this.$store.getters[`${this.storePath}/itemContent`](this.uniqueId)
            }
        },
        watch: {
            blockContent(val, old) {
                this.updateItemContent({content:  val, uniqueId: this.uniqueId})
            }
        },
        methods: {
            updateBlockContent(operation) {
                // this.blockContent = operation.event.srcElement.innerHTML
                this.blockContent = operation.api.origElements.innerHTML
            }
        },
        mounted() {
            if(this.freshComponent) {
                this.$refs['text-editor-' + this.uniqueId].api.elements[0].focus()
            }
        }
    }
</script>